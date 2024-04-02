<?php
final class FileUpload
{
    public function upload($userName)
    {
        try {
            //code...
            $fileName = $_FILES['image']['name'];
            $fileTmpName = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileError = $_FILES['image']['error'];
            $fileType = $_FILES['image']['type'];
            $fileExtension = explode('.', $fileName);
            $fileActualExtension = strtolower(end($fileExtension)); // jpeg

            $allowed = array('jpeg', 'jpg', 'png');

            if (in_array($fileActualExtension, $allowed)) {
                if ($fileError === 0) {
                    if ($fileSize < 50000000000) { // 500kb =  500000b 
                        $nameArr = explode(' ', $userName);
                        $fileNameNew = strtolower($nameArr[0]) . "_" . uniqid('') . "." . $fileActualExtension;
                        $fileDestination = '../../Images/' . $fileNameNew;
                        if (!file_exists($fileName)) {
                            if (move_uploaded_file(
                                $fileTmpName,
                                $fileDestination
                            )) {
                                // echo "Successfully uploaded your image";
                                return $fileNameNew;
                            } else {
                                $imageErr =  "Failed to upload your image";
                                return $imageErr;
                            }
                        } else {
                            $imageErr = "File already exists!";
                            return $imageErr;
                        }
                    } else {
                        $imageErr = "FILE TOO LARGE!";
                        return $imageErr;
                    }
                } else {
                    $imageErr = "There was file error";
                    return $imageErr;
                }
            } else {
                $imageErr = "Only .png, .jpg, .jpeg supported";
                return $imageErr;
            }
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }
}
