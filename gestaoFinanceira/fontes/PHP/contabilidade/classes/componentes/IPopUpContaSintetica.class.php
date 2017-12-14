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
    * Data de Criação: 04/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    * $Id: IPopUpContaSintetica.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class  IPopUpContaSintetica extends BuscaInner
{

    public $stTipoBusca;
    public $obCmbEntidades;

    public function setTipoBusca($valor) { $this->stTipoBusca = $valor;         }

    public function getTransacao() { return $this->obTransacao;                   }

    public function IPopUpContaSintetica()
    {

        parent::BuscaInner();

        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;

        $obTAdministracaoConfiguracao->setDado( "cod_modulo", 9);
        $obTAdministracaoConfiguracao->setDado( "exercicio", Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->pegaConfiguracao( $stMascara, "masc_plano_contas" );

        $this->setRotulo( "Conta Sintética" );
        $this->setTitle( "Informe a Conta Sintética." );
        $this->setNull( true );
        $this->setId( "stNomContaSintetica" );
        $this->setValue( '' );
        $this->obCampoCod->setName("inCodContaSintetica");
        $this->obCampoCod->setId("inCodContaSintetica");
        $this->obCampoCod->setSize     ( 25 );
        $this->obCampoCod->setValue( "" );
        $this->obCampoCod->setMascara( $stMascara );
        $this->obCampoCod->setPreencheComZeros ( 'D' );
        $this->stTipoBusca = "conta_sintetica";

    }

    public function montaHTML()
    {
        $pgOcul = "'".CAM_GF_CONT_PROCESSAMENTO."OCContaSintetica.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=N'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'".$this->stTipoBusca."');" );
        $this->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/LSContaSintetica.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$this->stTipoBusca."','".Sessao::getId()."','800','');");
        parent::montaHTML();

    }
}
?>
