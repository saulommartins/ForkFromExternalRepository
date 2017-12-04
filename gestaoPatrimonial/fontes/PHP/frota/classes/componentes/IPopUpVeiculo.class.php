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
    * Data de Criação: 19/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: IPopUpVeiculo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpVeiculo extends BuscaInner
{
    public $stTipoConsulta;   //// se estiver vazia a consulta pegar qualquer tipo de veiculo

    public function setTipoConsulta($valor) { $this->stTipoConsulta = $valor; }
    public function getTipoConsulta() { return $this->stTipoConsulta;            }
    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpVeiculo($obForm)
    {
        parent::BuscaInner();
        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Veículo'           );
        $this->setTitle                  ( 'Informe o veículo.');
        $this->setId                     ( 'stNomVeiculo'      );
        $this->setNull                   ( true                );
        $this->obCampoCod->setName       ( "inCodVeiculo"      );
        $this->obCampoCod->setId         ( "inCodVeiculo"      );
        $this->obCampoCod->setAlign      ( "left"              );
        $this->obImagem->setId           ( "imgVeiculo"        );

    }

    public function montaHTML()
    {
        $pgOcul = "'".CAM_GP_FRO_INSTANCIAS."processamento/OCProcurarVeiculo.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getId()."&stIdCampoDesc=".$this->getId()."&stTipoConsulta=".$this->getTipoConsulta()."'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaPopup');" );

        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_FRO_POPUPS . "veiculo/FLProcurarVeiculo.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() ."&stTipoConsulta=".$this->getTipoConsulta()."','800','550');");

        parent::montaHTML();
    }
}
?>
