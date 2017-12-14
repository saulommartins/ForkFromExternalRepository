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
    * Formulário do TCE-AL Relacionar Margem Consignada
    * Data de Criação: 26/09/2014

    * @author Analista
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: FMManterMargemConsignada.php 60066 2014-09-26 18:45:51Z michel $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );

$stPrograma = 'ManterMargemConsignada';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

Sessao::write('inQtnEventos', 10);

$stAcao = $request->get('stAcao');

$jsOnload = "montaParametrosGET('atualizarEventos','stAcao');";

$dtVigencia = date('d/m/Y');
if (isset($_REQUEST["dtVigencia"])) {
    $dtVigencia = $_REQUEST["dtVigencia"];
}
Sessao::write("dtVigencia",$dtVigencia);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

// Busca a entidade definida como prefeitura na configuração do orçamento
$stCampo   = "valor";
$stTabela  = "administracao.configuracao";
$stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= "   AND parametro = 'cod_entidade_prefeitura' ";

$inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($_REQUEST['inCodEntidade'] == $inCodEntidadePrefeitura) {
    $stFiltro = " WHERE nspname = 'pessoal'";
    $stSchema = '';
} else {
    $stFiltro = " WHERE nspname = 'pessoal_".$_REQUEST['inCodEntidade']."'";
    $stSchema = '_'.$_REQUEST['inCodEntidade'];
}

$obTEntidade = new TEntidade();
$obTEntidade->recuperaEsquemasCriados($rsEsquemas, $stFiltro);

// Verifica se existe o schema para a entidade selecionada
if ($rsEsquemas->getNumLinhas() < 1) {
    SistemaLegado::alertaAviso($pgFilt.'?stAcao='.$_REQUEST['stAcao'], 'Não existe entidade criada no RH para a entidade selecionada!' , '', 'aviso', Sessao::getId(), '../');
}

$stSessaoEntidade = Sessao::getEntidade();
$stSessaoEntidade2 = ($stSchema!='') ? $_REQUEST['inCodEntidade'] : $stSchema;
Sessao::setEntidade($stSessaoEntidade2);
Sessao::write('stEntidadeSessao', $stSessaoEntidade2);

$obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
$obRFolhaPagamentoEvento->setNatureza('D');
$obRFolhaPagamentoEvento->listarEvento($rsEventos);

// Se não foi selecionada a entidade definida como prefeitura
// ao executar as consultas, automaticamente é adicionado o "_" + cod_entidade selecionada
if ($_REQUEST['inCodEntidade'] != $inCodEntidadePrefeitura) {
    $arSchemasRH = array();
    $obTEntidade->recuperaSchemasRH($rsSchemasRH);
    while (!$rsSchemasRH->eof()) {
        $arSchemasRH[] = $rsSchemasRH->getCampo("schema_nome");
        $rsSchemasRH->proximo();
    }
    Sessao::write('arSchemasRH', $arSchemasRH, true);
}

$obHdnSchema = new Hidden;
$obHdnSchema->setName ('stSchema');
$obHdnSchema->setValue($stSchema);

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ('inCodEntidade');
$obHdnEntidade->setValue($_REQUEST['inCodEntidade']);

$obCmbEvento = new SelectMultiplo();
$obCmbEvento->setName                           ( 'inCodEvento'                                             );
$obCmbEvento->setRotulo                         ( "Eventos de Empréstimos Consignados"                      );
$obCmbEvento->setTitle                          ( "Selecione os eventos a serem apresentados no relatório." );
$obCmbEvento->SetNomeLista1                     ( 'inCodEventoDisponiveis'                                  );
$obCmbEvento->setCampoId1                       ( '[cod_evento]'                                            );
$obCmbEvento->setCampoDesc1                     ( '[codigo]-[descricao]'                                    );
$obCmbEvento->setStyle1                         ( "width: 300px"                                            );
$obCmbEvento->SetRecord1                        ( $rsEventos                                                );
$obCmbEvento->SetNomeLista2                     ( 'inCodEventoSelecionados'                                 );
$obCmbEvento->setCampoId2                       ( '[cod_evento]'                                            );
$obCmbEvento->setCampoDesc2                     ( '[codigo]-[descricao]'                                    );
$obCmbEvento->setStyle2                         ( "width: 300px"                                            );
$obCmbEvento->SetRecord2                        ( new recordset()                                           );
$obCmbEvento->setNull                           ( false                                                     );
$obCmbEvento->obSelect1->setSize                ( 5                                                         );
$obCmbEvento->obSelect2->setSize                ( 5                                                         );

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnEntidade);
$obFormulario->addComponente ( $obCmbEvento );
$obFormulario->Cancelar ($pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao);

$obFormulario->show();

$arSelectMultiploEventos = array();
array_push($arSelectMultiploEventos, $obCmbEvento);
Sessao::write("arSelectMultiploEventos", $arSelectMultiploEventos);

Sessao::setEntidade($stSessaoEntidade);

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
