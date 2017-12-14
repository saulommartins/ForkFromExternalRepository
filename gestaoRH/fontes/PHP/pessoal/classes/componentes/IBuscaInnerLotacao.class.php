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
    * Classe do componente Lotacao
    * Data de Criação: 06/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php" );

class IBuscaInnerLotacao extends Objeto
{
/**
    * @access Private
    * @var Objeto
*/
var $obBscLotacao;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbLotacao;
var $obHdnCodOrgao;

/**
    * @access Public
    * @param Objeto $Valor
*/
function setLotacao($valor) { $this->obBscLotacao = $valor; }

/**
    * @access Public
    * @return Objeto
*/
function getLotacao() { return $this->obBscLotacao; }

/**
    * Método construtor
    * @access Private
*/
function IBuscaInnerLotacao($opcoes = array())
{
    $inCodLotacao = null;

    if (!isset($opcoes['extensao'])) {
        $opcoes['extensao'] = "";
    }

    if ($opcoes['cod_organograma'] == "") {
        $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
        $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);
        $opcoes['cod_organograma'] = $rsOrganogramaVigente->getCampo('cod_organograma');
    }

    $stFiltro = " AND orgao_nivel.cod_organograma = ".$opcoes['cod_organograma'];

    $obTOrganogramaOrgao = new TOrganogramaOrgao;
    $obTOrganogramaOrgao->setDado('vigencia', date('Y-m-d'));
    $obTOrganogramaOrgao->recuperaOrgaos( $rsOrganogramaOrgao, $stFiltro, " LIMIT 1 " );

    $stMascLotacao   = strtr  ( $rsOrganogramaOrgao->getCampo('cod_estrutural') , "012345678" , "999999999" );
    $inMaxLenLotacao = strlen ( $stMascLotacao );

    $pgOcul = "'".CAM_GRH_PES_PROCESSAMENTO."OCIBuscaInnerLotacao.php?".Sessao::getId()."&'+this.name+'='+this.value+'&stExtensao=".$opcoes['extensao']."&inCodOrganograma=".$opcoes['cod_organograma']."'";

    $this->setLotacao( new BuscaInner );
    $this->obBscLotacao->setRotulo                         ( "Lotação"                             );
    $this->obBscLotacao->setTitle                          ( "Selecione a lotação."                );
    $this->obBscLotacao->setId                             ( "stLotacao".$opcoes['extensao']       );
    $this->obBscLotacao->obCampoCod->setName               ( "inCodLotacao".$opcoes['extensao']    );
    $this->obBscLotacao->obCampoCod->setValue              ( $inCodLotacao                         );
    $this->obBscLotacao->obCampoCod->setPreencheComZeros   ( 'D'                                   );
    $this->obBscLotacao->obCampoCod->setMaxLength          ( $inMaxLenLotacao                      );
    $this->obBscLotacao->obCampoCod->setMascara            ( $stMascLotacao                        );
    $this->obBscLotacao->obCampoCod->setSize               ( 10                                    );
    $this->obBscLotacao->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherLotacao');"          );
    $this->obBscLotacao->obCampoCod->obEvento->setOnBlur   ( "ajaxJavaScript($pgOcul,'preencherLotacao');"          );
    $this->obBscLotacao->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarLotacao.php','frm','inCodLotacao".$opcoes['extensao']."','stLotacao".$opcoes['extensao']."','','".Sessao::getId()."&inCodOrganograma=".$opcoes['cod_organograma']."','800','550')" );

}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponente($this->obBscLotacao);
}

}
?>
