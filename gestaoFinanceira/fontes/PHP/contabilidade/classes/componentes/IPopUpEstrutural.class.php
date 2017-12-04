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
* Arquivo de popup de busca de Recurso
* Data de Criação: 12/05/2008

* @author Analista: Tonismar RÃ©gis Bernardo
* @author Desenvolvedor: Grasiele Torres

* @package URBEM
* @subpackage

* $Id: IPopUpEstrutural.class.php 30739 2008-07-03 18:12:09Z girardi $

 Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class  IPopUpEstrutural extends BuscaInner
{

    public function IPopUpEstrutural()
    {

        parent::BuscaInner();

        isset($_REQUEST['inExercicio']) ? $stExercicio = $_REQUEST['inExercicio'] : Sessao::getExercicio();

        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $obTAdministracaoConfiguracao->setDado( "cod_modulo", 9);
        $obTAdministracaoConfiguracao->setDado( "exercicio", $stExercicio );
        $obTAdministracaoConfiguracao->pegaConfiguracao( $stMascara, "masc_plano_contas" );

        $this->setRotulo               ( "Código Estrutural" );
        $this->setTitle                ( "Informe o código estrutural da conta contábil." );
        $this->setNull                 ( true );
        $this->setId                   ( "stDescricaoClassificacao" );
        $this->obCampoCod->setName     ( "stCodEstrutural" );
        $this->obCampoCod->setValue    ( "" );
        $this->obCampoCod->setAlign    ("left");
        $this->obCampoCod->setMascara  ( $stMascara );
        $this->obCampoCod->setPreencheComZeros ( 'D' );

    }

    public function montaHTML()
    {
        
        if( isset($_REQUEST['inExercicio']) )
            $stExercicio = "&stExercicio=".$_REQUEST['inExercicio'];
        else
            $stExercicio ="";

        $pgOcul = "'".CAM_GF_CONT_PROCESSAMENTO."OCEstruturalPlano.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'".$stExercicio."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
        $this->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLEstrutural.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','estrutural".$stExercicio."','".Sessao::getId()."','800','550');");
        parent::montaHTML();
    }
}
?>
