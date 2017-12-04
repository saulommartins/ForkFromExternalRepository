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

class ImageBox extends Componente
{
    protected $flLarguraBox;
    protected $flAlturaBox;
    protected $flAlturaImagem;
    protected $flLarguraImagem;
    protected $flAlturaMaxImagem;
    protected $flLarguraMaxImagem;
    protected $flAlturaImagemPopUp;
    protected $flLarguraImagemPopUp;
    protected $flAlturaMaxImagemPopUp;
    protected $flLarguraMaxImagemPopUp;
    protected $stCaminhoImagem;
    protected $arImagem;

    public function setLarguraBox($stValor) {$this->flLarguraBox            = $stValor;}
    public function setAlturaBox($stValor) {$this->flAlturaBox             = $stValor;}
    public function setAlturaImagem($stValor) {$this->flAlturaImagem          = $stValor;}
    public function setLarguraImagem($stValor) {$this->flLarguraImagem         = $stValor;}
    public function setAlturaMaxImagem($stValor) {$this->flAlturaMaxImagem       = $stValor;}
    public function setLarguraMaxImagem($stValor) {$this->flLarguraMaxImagem      = $stValor;}
    public function setLarguraImagemPopUp($stValor) {$this->flLarguraImagemPopUp    = $stValor;}
    public function setAlturaImagemPopUp($stValor) {$this->flAlturaImagemPopUp     = $stValor;}
    public function setLarguraMaxImagemPopUp($stValor) {$this->flLarguraMaxImagemPopUp = $stValor;}
    public function setAlturaMaxImagemPopUp($stValor) {$this->flAlturaMaxImagemPopUp  = $stValor;}
    public function setCaminhoImagem($stValor) {$this->stCaminhoImagem         = $stValor;}
    public function setId($stValor) {$this->stId                    = $stValor;}

    public function getLarguraBox() {return $this->flLarguraBox;           }
    public function getAlturaBox() {return $this->flAlturaBox;            }
    public function getAlturaImagem() {return $this->flAlturaImagem;         }
    public function getLarguraImagem() {return $this->flLarguraImagem;        }
    public function getAlturaMaxImagem() {return $this->flAlturaMaxImagem;      }
    public function getLarguraMaxImagem() {return $this->flLarguraMaxImagem;     }
    public function getLarguraImagemPopUp() {return $this->flLarguraImagemPopUp;   }
    public function getAlturaImagemPopUp() {return $this->flAlturaImagemPopUp;    }
    public function getLarguraMaxImagemPopUp() {return $this->flLarguraMaxImagemPopUp;}
    public function getAlturaMaxImagemPopUp() {return $this->flAlturaMaxImagemPopUp; }
    public function getCaminhoImagem() {return $this->stCaminhoImagem;        }
    public function getId() {return $this->stId;                   }

    public function addImagem($stDescricao, $obImagem,$stRotuloFuncao='',$stFuncao='')
    {
        $this->arImagem[] = array(  0=>$stDescricao,'descricao'=>$stDescricao,
                                    1=>$obImagem,'objeto'=>$obImagem,
                                    2=>$stRotuloFuncao,'rotulo',$stRotuloFuncao,
                                    3=>$stFuncao,'funcao',$stFuncao );
    }

    public function __Construct()
    {
        parent::Componente();
        $this->setName      ( "ImageBox" );
        $this->setLarguraBox("50%");
        $this->setAlturaBox (80);
        $this->setAlturaImagem(55);
        $this->setLarguraImagem('');
        $this->setAlturaMaxImagem('');
        $this->setLarguraMaxImagem('');
        $this->setLarguraImagemPopUp(380);
        $this->setAlturaImagemPopUp('');
        $this->setLarguraMaxImagemPopUp('');
        $this->setAlturaMaxImagemPopUp(250);
        $this->setDefinicao ( "button" );
        $this->arImagem = array();
        $this->setId('imageBox');
    }

    public function montaHtml()
    {
        $this->montaHtmlFotos();
        ob_start();
?>
<div style="float:left; width:<?=$this->flLarguraBox;?>; height:<?=$this->flAlturaBox;?>;overflow: scroll; overflow-y : hidden;border:1px solid #000; white-space:nowrap;" id="<?=$this->stId;?>">
    <?=$this->getHtml();?>
</div>
<?php
        $this->setHtml(ob_get_clean());
    }

    public function montaHtmlFotos()
    {
        ob_start();
        foreach ($this->arImagem AS $arImagem) {
            $stURL = $arImagem['objeto']->getCaminho().'&boBox=true';
            $arImagem['objeto']->setCaminho($stURL);
            $stURLPopUp = $arImagem['objeto']->getCaminho().'&boBox=false';
            $arImagem['objeto']->obEvento->setOnClick("imagemPopUp('".$arImagem[0]."','".$stURLPopUp."','".$arImagem[2]."','".$arImagem[3]."')");
            $arImagem['objeto']->show();
        }
        $this->setHtml(ob_get_clean());
    }

    public function ajustaTamanhoImagem(&$stImagem,$boBox='true',$inH='',$inW='',$inHMax='',$inWMax='')
    {
        if ($boBox=='true') {
            $inH=    $this->getAlturaImagem();
            $inW=    $this->getLarguraImagem();
            $inHMax= $this->getAlturaMaxImagem();
            $inWMax= $this->getLarguraMaxImagem();
        } elseif ($boBox=='false') {
            $inH=    $this->getAlturaImagemPopUp();
            $inW=    $this->getLarguraImagemPopUp();
            $inHMax= $this->getAlturaMaxImagemPopUp();
            $inWMax= $this->getLarguraMaxImagemPopUp();
        }

        if ($inH != '' and $inW == '') {
            $flPerc=100*$inH/imagesy($stImagem);
            $inW=$flPerc*imagesx($stImagem)/100;
            if ($inWMax != '' and $inW > $inWMax) {
                $this->ajustaTamanhoImagem($stImagem,'','',$inWMax,'','');
            } else {
                $flPerc=100*$inH/imagesy($stImagem);
                $inW=$flPerc*imagesx($stImagem)/100;
                $thumb   = imagecreatetruecolor($inW,$inH);
                imagecopyresized($thumb, $stImagem, 0, 0, 0, 0,$inW,$inH, imagesx($stImagem),imagesy($stImagem));
                imagejpeg($thumb);
                imagedestroy($thumb);
            }
        } elseif ($inH == '' and $inW != '') {
            $flPerc=100*$inW/imagesx($stImagem);
            $inH=$flPerc*imagesy($stImagem)/100;
            if ($inHMax != '' and $inH > $inHMax) {
                $this->ajustaTamanhoImagem($stImagem,'',$inHMax,'','','');
            } else {
                $inH=$flPerc*imagesy($stImagem)/100;
                $inW=$flPerc*imagesx($stImagem)/100;
                $thumb   = imagecreatetruecolor($inW,$inH);
                imagecopyresized($thumb, $stImagem, 0, 0, 0, 0,$inW,$inH, imagesx($stImagem),imagesy($stImagem));
                imagejpeg($thumb);
                imagedestroy($thumb);
            }
        }
    }

    public function show()
    {
        $this->montaHtml();
        $stHtml = $this->getHtml();
        $stHtml =  trim( $stHtml )."\n";
        echo $stHtml;
    }

}

?>
