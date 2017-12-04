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
  * Página de relatório de recibo extra
  * Data de Criação: 16/09/2009
  * @author Analista:      Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
  * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
  * $Id:$
  */
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
require_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, 'and e.cod_entidade in ('.implode(',',$_POST['inCodEntidade']).')');

$arEntidade = array();
while (!$rsEntidade->eof()) {
    $arEntidade[] = $rsEntidade->getCampo('nom_cgm');
    $rsEntidade->proximo();
}

$preview = new PreviewBirt(2, 30, 6);
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);
$preview->setTitulo('Recibo Extra');
$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->addParametro('inCodEntidade'      , implode(',', $_POST['inCodEntidade']));
$preview->addParametro('stNomeEntidade'     , implode(',', $arEntidade));
$preview->addParametro('stExercicio'        , $_POST['stExercicio']);
$preview->addParametro('stDataInicial'      , $_POST['stDataInicial1']);
$preview->addParametro('stDataFinal'        , $_POST['stDataFinal1']);

$preview->addParametro('stDataInicialBaixa' , $_POST['stDataInicial2']);
$preview->addParametro('stDataFinalBaixa'   , $_POST['stDataFinal2']);

$preview->addParametro('stOrdenacao'        , $_POST['stOrdenacao']);
$preview->addParametro('stBaixado'          , $_POST['stBaixado']);
$preview->addParametro('stDestinacaoRecurso', $_POST['stDestinacaoRecurso']);
$preview->addParametro('inCodDetalhamento'  , $_POST['inCodDetalhamento']);
$preview->addParametro('inCodContaAnalitica', $_POST['inCodContaAnalitica']);
$preview->addParametro('stNomContaAnalitica', $_POST['stNomContaAnalitica']);
$preview->addParametro('stTipoDemonstracao' , $_POST['stTipoDemonstracao']);
$preview->addParametro('inCodContaBanco'    , $_POST['inCodContaBanco']);
$preview->addParametro('stNomContaBanco'    , $_POST['stNomContaBanco']);
$preview->addParametro('inCodRecurso'       , $_POST['inCodRecurso']);
$preview->addParametro('stDescricaoRecurso' , $_POST['stDescricaoRecurso']);
$preview->addParametro('inCodCredor'        , $_POST['inCodCredor']);
$preview->addParametro('stNomCredor'        , $_POST['stNomCredor']);
$preview->preview();
