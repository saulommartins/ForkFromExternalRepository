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
/*
    * Formulário de Configuração do arquivo de licitações
    * Data de Criação   : 29/03/2011

    * @author: Eduardo Paculski Schitz

    * @ignore
    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES . '/Table/Table.class.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMConfiguracaoArquivoLicitacao.class.php';

$stPrograma = 'ConfigurarArquivoLicitacao';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;

$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao );

$obTTCEAMConfiguracaoArquivoLicitacao = new TTCEAMConfiguracaoArquivoLicitacao;
$obTTCEAMConfiguracaoArquivoLicitacao->setDado('exercicio'   , Sessao::getExercicio());
$obTTCEAMConfiguracaoArquivoLicitacao->setDado('cod_entidade', implode(',', $_REQUEST['inCodEntidade']));
$obTTCEAMConfiguracaoArquivoLicitacao->setDado('mes'         , $_REQUEST['inMes']);
$obTTCEAMConfiguracaoArquivoLicitacao->recuperaConfiguracaoArquivoLicitacao($rsConfiguracao);

$obTxtDiarioOficial = new TextBox;
$obTxtDiarioOficial->setName ('stDiarioOficial_[cod_mapa]_[exercicio]');
$obTxtDiarioOficial->setId   ('stDiarioOficial_[cod_mapa]_[exercicio]');
$obTxtDiarioOficial->setValue('[diario_oficial]');
$obTxtDiarioOficial->setSize (10);
$obTxtDiarioOficial->setInteiro(true);
$obTxtDiarioOficial->setMaxLength(6);

$obDtPublicacaoHomologacao = new Data;
$obDtPublicacaoHomologacao->setName ('dtPublicacaoHomologacao_[cod_mapa]_[exercicio]');
$obDtPublicacaoHomologacao->setId   ('dtPublicacaoHomologacao_[cod_mapa]_[exercicio]');
$obDtPublicacaoHomologacao->setValue('[dt_publicacao_homologacao]');

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Licitações/Compras Diretas');
$obLista->setRecordSet($rsConfiguracao);
//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Compra Direta/Licitação', 35);
$obLista->addCabecalho('Modalidade', 35);
$obLista->addCabecalho('Diário Oficial', 10);
$obLista->addCabecalho('Data Publicação da Homologação', 15);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[licitacao_compra_direta]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[modalidade]');
$obLista->commitDado();

$obLista->addDadoComponente($obTxtDiarioOficial, false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo("diario_oficial");
$obLista->commitDadoComponente();

$obLista->addDadoComponente($obDtPublicacaoHomologacao, false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo("dt_publicacao_homologacao");
$obLista->commitDadoComponente();

$obSpnLista = new Span();
$obSpnLista->setId('spnCodigos');
$obLista->montaHTML();
$obSpnLista->setValue($obLista->getHTML());

$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addSpan  ($obSpnLista);
$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
