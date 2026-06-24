import { Inter } from "next/font/google";
import "./globals.css";

const inter = Inter({ subsets: ["latin"] });

export const metadata = {
  title: "Sistem Inventaris Aset | MAN 2 HSU",
  description: "Aplikasi Manajemen Inventaris Aset dan Peminjaman Barang di MAN 2 Hulu Sungai Utara.",
};

export default function RootLayout({ children }) {
  return (
    <html lang="id">
      <body className={inter.className}>
        {children}
      </body>
    </html>
  );
}
