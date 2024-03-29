<template>
  <div class="container my-5">
    <div class="row d-flex justify-content-end">
      <div class="col-3">
        <button class="btn btn-sm btn-primary" @click="createForm()">
          <i class="fa-sharp fa-solid fa-plus"></i> Create
        </button>
        <button class="btn btn-sm btn-info ml-1" @click="exportExcel()">
          <i class="fa-solid fa-file-arrow-down"></i> Export
        </button>
        <button
          type="button"
          class="btn btn-secondary btn-sm ml-1"
          data-bs-toggle="modal"
          data-bs-target="#importModal"
          data-bs-whatever="@getbootstrap"
        >
          <i class="fa-solid fa-file-arrow-up"></i> Import
        </button>
        <div
          class="modal fade"
          id="importModal"
          tabindex="-1"
          aria-labelledby="importModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Categories</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                  id="modal-close"
                  @click="clearErrMsg()"
                ></button>
              </div>
              <div class="modal-body">
                <div
                  class="alert alert-sm alert-warning alert-dismissible fade show"
                  role="alert"
                  v-if="errors != null"
                >
                  <small v-for="(error, index) in errors" :key="index" class="text-danger"
                    >*{{ error[0] }}<br
                  /></small>
                  <button
                    type="button"
                    class="btn-close btn-sm"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                  ></button>
                </div>
                <form id="import-form">
                  <div class="mb-3">
                    <label for="file" class="col-form-label">Choose excel file:</label>
                    <input class="form-control" type="file" name="file" id="file" />
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="importExcel()"
                >
                  Submit
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-5 d-flex justify-content-end">
        <div class="input-group input-group-sm mb-3 w-50">
          <input
            type="text"
            class="form-control"
            v-model="keyword"
            placeholder="Search"
            @keyup.enter="search()"
          />
          <button class="input-group-text bg-primary text-white" @click="search()">
            <i class="fa-sharp fa-solid fa-magnifying-glass"></i> Search
          </button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-4 px-3">
        <div class="card">
          <div class="card-header">
            <h5 class="fw-bolder">{{ !isEditMode ? "Create" : "Edit" }}</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="isEditMode ? editCategory() : createNew()">
              <div class="form-group mb-3">
                <label for="name" class="fw-semibold">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  placeholder="Enter Category Name!"
                  v-model="category.name"
                />
                <small class="text-danger" v-if="nameErr != ''">*{{ nameErr }}</small>
              </div>
              <button class="btn btn-sm btn-primary" type="submit">
                <i class="fa-regular fa-floppy-disk"></i> Save
              </button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-8">
        <b-table
          striped
          id="my-table"
          :items="categories"
          :per-page="perPage"
          :current-page="currentPage"
          :fields="fields"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"
          class="mb-5"
        >
          <template #cell(actions)="data">
            <button class="btn btn-sm btn-success" @click="editForm(data.item)">
              <i class="fa-regular fa-pen-to-square"></i> Edit
            </button>
            <button class="btn btn-sm btn-danger" @click="destory(data.item)">
              <i class="fa-solid fa-trash-can"></i> Delete
            </button>
          </template>
        </b-table>
        <p v-if="rows == 0 && keyword != ''" class="text-danger text-center">
          No category here!
        </p>
        <b-pagination
          v-model="currentPage"
          :total-rows="rows"
          :per-page="perPage"
          :current-page="currentPage"
          first-text="First"
          prev-text="Prev"
          next-text="Next"
          last-text="Last"
          v-if="rows > 5"
        ></b-pagination>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";

const Toast = Swal.mixin({
  toast: true,
  position: "top-right",
  customClass: {
    popup: "colored-toast",
  },
  showConfirmButton: false,
  timer: 1500,
  timerProgressBar: true,
});
export default {
  head: {
    title: "Category",
  },
  data() {
    return {
      sortBy: "id",
      sortDesc: true,
      fields: [
        { key: "id", label: "#" },
        { key: "name", label: "Name", thStyle: { width: "70%" } },
        ,
        "Actions",
      ],
      currentPage: 1,
      perPage: 5,
      category: {
        id: null,
        name: "",
      },
      categories: [],
      nameErr: "",
      isEditMode: false,
      keyword: "",
      file: null,
      errors: null,
    };
  },
  async fetch() {
    this.nameErr = "";
    this.getAllCategories();
    this.$axios.$get("http://localhost:8000/sanctum/csrf-cookie");
  },
  computed: {
    rows() {
      return this.categories.length;
    },
  },
  methods: {
    async getAllCategories() {
      await this.$axios
        .$get("http://127.0.0.1:8000/api/categories?search=" + this.keyword)
        .then((res) => {
          this.categories = res;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    async createNew() {
      await this.$axios
        .$post("categories", { name: this.category.name })
        .then((res) => {
          this.categories.push(res.data);
          this.category.name = "";
          this.nameErr = "";
          Toast.fire({
            icon: "success",
            title: "Created Successfully!",
          });
        })
        .catch((err) => {
          this.nameErr = err.response.data.errors.name[0];
        });
    },
    createForm() {
      this.isEditMode = false;
      this.category = {};
      this.nameErr = "";
    },
    editForm(item) {
      this.isEditMode = true;
      this.nameErr = "";
      this.category = { ...item };
    },
    editCategory() {
      this.$axios
        .$put(`categories/${this.category.id}`, this.category)
        .then((res) => {
          this.isEditMode = false;
          this.getAllCategories();
          this.category = {};
          this.nameErr = "";
          Toast.fire({
            icon: "success",
            title: "Updated Successfully!",
          });
        })
        .catch((err) => {
          this.nameErr = err.response.data.errors.name[0];
        });
    },
    destory(item) {
      if (confirm("Are you sure to delete?")) {
        this.$axios
          .$delete(`categories/${item.id}`)
          .then((res) => {
            this.categories = this.categories.filter((category) => {
              return category.id !== item.id;
            });
            Toast.fire({
              icon: "warning",
              title: "Deleted Successfully!",
            });
          })
          .catch((err) => {
            console.log(err);
          });
      }
    },
    search() {
      this.getAllCategories();
    },
    exportExcel() {
      this.$axios
        .$post(
          "http://127.0.0.1:8000/api/categories/export",
          { keyword: this.keyword },
          { responseType: "arraybuffer" }
        )
        .then((response) => {
          let fileURL = window.URL.createObjectURL(new Blob([response]));
          let fileLink = document.createElement("a");
          fileLink.href = fileURL;
          fileLink.setAttribute("download", "categories.xlsx");
          document.body.appendChild(fileLink);
          fileLink.click();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    importExcel() {
      this.errors = null;
      let form = document.getElementById("import-form");
      let formData = new FormData(form);
      this.$axios
        .$post("http://127.0.0.1:8000/api/categories/import", formData)
        .then((res) => {
          document.getElementById("modal-close").click();
          form.reset();
          this.getAllCategories();
          Toast.fire({
            icon: "success",
            title: "Imported Successfully!",
          });
        })
        .catch((err) => {
          if (err.response.status == 422) {
            this.errors = err.response.data.errors;
            console.log(this.errors);
          }
        });
    },
    clearErrMsg() {
      this.errors = null;
      let form = document.getElementById("import-form");
      form.reset();
    },
  },
};
</script>

<style></style>
