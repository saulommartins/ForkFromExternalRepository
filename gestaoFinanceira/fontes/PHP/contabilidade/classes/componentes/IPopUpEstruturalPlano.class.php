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
* Data de Criação: 20/06/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: José Eduardo Porto

* @package URBEM
* @subpackage

* Id: $

 Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );


class  IPopUpEstruturalPlano extends BuscaInner
{
    var $stEscrituracao = '';
    public function getTipoEscrituracao(){ return $this->stEscrituracao;}
    public function setTipoEscrituracao( $valor ){ $this->stEscrituracao = $valor; }

    public function IPopUpEstruturalPlano()
    {
        parent::BuscaInner();

        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $obTAdministracaoConfiguracao->setDado( "cod_modulo", 9);
        $obTAdministracaoConfiguracao->setDado( "exercicio", Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->pegaConfiguracao( $stMascara, "masc_plano_contas" );

        $this->setRotulo               ( "Código Estrutural" );
        $this->setTitle                ( "Informe o código estrutural da conta contábil." );
        $this->setNull                 ( true );
        $this->setId                   ( "stDescricaoClassificacao" );
        $this->setTipoEscrituracao     ( "estrutural" );
        $this->obCampoCod->setName     ( "stCodEstrutural" );
        $this->obCampoCod->setValue    ( "" );
        $this->obCampoCod->setAlign    ("left");
        $this->obCampoCod->setMascara  ( $stMascara );
        $this->obCampoCod->setPreencheComZeros ( 'D' );

    }

    public function montaHTML()
    {
        $pgOcul = "'".CAM_GF_CONT_PROCESSAMENTO."OCEstruturalPlano.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stEscrituracao=".$this->getTipoEscrituracao()."'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
        $this->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$this->getTipoEscrituracao()."','".Sessao::getId()."','800','550');");
        parent::montaHTML();
    }
}
?>
