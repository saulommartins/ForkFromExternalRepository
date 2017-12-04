<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
 * FDrawing.php
 *--------------------------------------------------------------------
 *
 * Holds the drawing $im
 * You can use get_im() to add other kind of form not held into these classes.
 *
 *--------------------------------------------------------------------
 * Revision History
 * v1.2.3b	31 dec	2005	Jean-Sébastien Goupil	Just one barcode per drawing
 * v1.2.1	27 jun	2005	Jean-Sébastien Goupil	Font support added
 * V1.00	17 jun	2004	Jean-Sebastien Goupil
 *--------------------------------------------------------------------
 * $Id: FDrawing.php 59612 2014-09-02 12:00:51Z gelson $
 * PHP5-Revision: 1.6
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://other.lookstrike.com/barcode/
 */
class FDrawing
{
    public $w, $h;		// int
    public $color;		// Fcolor
    public $filename;		// char *
    public $im;		// {object}
    public $barcode;		// BarCode

    /**
     * Constructor
     *
     * @param int    $w
     * @param int    $h
     * @param string filename
     * @param FColor $color
     */
    public function FDrawing($filename, &$color)
    {
        $this->filename = $filename;
        $this->color =& $color;
    }

    /**
     * Destructor
     */
    //public function __destruct() {
    //	$this->destroy();
    //}

    /**
     * Init Image and color background
     */
    public function init()
    {
        $this->im = imagecreatetruecolor($this->w, $this->h);
        //or die('Can\'t Initialize the GD Libraty');
        imagefill($this->im, 0, 0, $this->color->allocate($this->im));
    }

    /**
     * @return resource
     */
    function &get_im() {
        return $this->im;
    }

    /**
     * @param resource $im
     */
    public function set_im(&$im)
    {
        $this->im = $im;
    }

    /**
     * Add barcode into the drawing array (for future drawing)
     * ! DEPRECATED !
     *
     * @param BarCode $barcode
     * @deprecated
     */
    public function add_barcode(&$barcode)
    {
        $this->setBarcode($barcode);
    }

    /**
     * Set Barcode for drawing
     *
     * @param BarCode $barcode
     */
    public function setBarcode(&$barcode)
    {
        $this->barcode =& $barcode;
    }

    /**
     * Draw first all forms and after all texts on $im
     * ! DEPRECATED !
     *
     * @deprecated
     */
    public function draw_all()
    {
        $this->draw();
    }

    /**
     * Draw the barcode on the image $im
     */
    public function draw()
    {
        $this->w = $this->barcode->getMaxWidth();
        $this->h = $this->barcode->getMaxHeight();
        $this->init();
        $this->barcode->draw($this->im);
    }

    /**
     * Save $im into the file (many format available)
     *
     * @param int $image_style
     * @param int $quality
     */
    public function finish($image_style = IMG_FORMAT_PNG, $quality = 100)
    {
        if ($image_style === constant('IMG_FORMAT_PNG')) {
            if (empty($this->filename)) {
                imagepng($this->im);
            } else {
                imagepng($this->im, $this->filename);
            }
        } elseif ($image_style === constant('IMG_FORMAT_JPEG')) {
            imagejpeg($this->im, $this->filename, $quality);
        }
    }

    /**
     * Free the memory of PHP (called also by destructor)
     */
    public function destroy()
    {
        @imagedestroy($this->im);
    }
};
