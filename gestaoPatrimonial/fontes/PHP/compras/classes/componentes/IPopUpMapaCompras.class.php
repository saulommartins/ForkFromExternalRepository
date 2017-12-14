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
    * Arquivo de popup de busca Mapa de Compras
    * Data de Criação: 23/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * Casos de uso: uc-03.04.05

    $Id: IPopUpMapaCompras.class.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once CLA_BUSCAINNER;

class  IPopUpMapaCompras extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    public $stTipoBusca;
    public $stExercicio;

    public function setTipoBusca($tipobusca) { $this->stTipoBusca = $tipobusca ; }
    public function getTipoBusca() { return $this->stTipoBusca ;       }

    public function setExercicio($stValor) { $this->stExercicio = $stValor;    }
    public function getExercicio() { return $this->stExercicio;        }

    public function setAutEmp($boAutEmp) { $this->boAutEmp = $boAutEmp;      }
    public function getAutEmp() { return $this->boAutEmp;           }

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpMapaCompras($obForm)
    {

        $ultCodMapa = SistemaLegado::pegaDado("cod_mapa","compras.mapa", " order by cod_mapa desc limit 1");

        if ($ultCodMapa != "") {
            $tam = strlen($ultCodMapa);
        } else {
            $tam = 1;
        }
    $stMascara = isset($stMascara) ? $stMascara : "";
        $stMascara = str_pad( $stMascara , $tam , '9' )."/9999";
        parent::BuscaInner();
        $this->obForm = $obForm;
        $this->setRotulo                ( 'Mapa de Compras'                        );
        $this->setTitle                 ( 'Selecione o Número do Mapa de Compras.' );
        $this->setId                    ( 'MapaCompras'                            );
        $this->setMostrarDescricao		( false		                               );
        $this->setAutEmp                ( false                                    );

        $this->setCampoCod				( new TextBox()         );
        $this->obCampoCod->setMinLength ( 1                     );
        $this->obCampoCod->setRotulo	( $this->getRotulo()    );
        $this->obCampoCod->setName      ( "stMapaCompras"       );
        $this->obCampoCod->setId        ( "stMapaCompras"       );
        $this->obCampoCod->setMascara   ( $stMascara            );

        $this->stTipo = $this->getTipoBusca();

    }

    public function montaHTML()
    {
        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_COM_POPUPS . "mapaCompras/FLBuscaMapaCompras.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId ."','&stTipoBusca=".$this->getTipoBusca()."&boAutEmp=".$this->getAutEmp()."&stExercicioMapa=".$this->getExercicio()."','". Sessao::getId() ."','800','550');");
        $this->setValoresBusca( CAM_GP_COM_POPUPS.'mapaCompras/OCBuscaMapaCompras.php?'.Sessao::getId()."&stExercicioMapa=".$this->getExercicio()   , $this->obForm->getName(), $this->getTipoBusca()   );

        parent::montaHTML();
    }
}
?>
