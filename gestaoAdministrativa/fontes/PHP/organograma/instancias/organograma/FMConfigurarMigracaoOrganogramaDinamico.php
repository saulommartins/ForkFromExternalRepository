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
 * Página de Formulário para Migrar Organograma
 * Data de criação : 03/04/2009

 * @author Analista: Gelson Wolowski
 * @author Programador: Diogo Zarpelon

 * @ignore

 $Id:$

 **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrganograma.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TConfigurarMigracaoOrganogramaDinamico.class.php';
include_once CAM_GA_ORGAN_NEGOCIO.'ROrganogramaOrganograma.class.php';

$stPrograma = "ConfigurarMigracaoOrganogramaDinamico";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include $pgJs;
$stAcao           = $_REQUEST["stAcao"];

$inCodOrganograma = $_REQUEST["inCodOrganograma"];
$arOrgaoSelecionado = Sessao::read('arOrgao');

# Inicializa se necessário o array.
if (!is_array($arOrgaoSelecionado)) {
   $arOrgaoSelecionado = array();
}

# Definição dos Componentes
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ('oculto');

$obTOrganogramaOrganograma = new TOrganogramaOrganograma;

# Filtra para listar somente organogramas com data menor ou igual ao dia.
$stFiltro = " WHERE ativo = true";
$obTOrganogramaOrganograma->recuperaOrganogramasAtivo($rsOrganogramaAtivo, '', '', $stFiltro);
$stOrganogramaAtivo = $rsOrganogramaAtivo->getCampo('cod_organograma')." - ".$rsOrganogramaAtivo->getCampo('implantacao');

# Label com o organograma ativo (apenas para informação)
$obLabelOrganogramaAtivo = new Label;
$obLabelOrganogramaAtivo->setRotulo('Organograma Ativo');
$obLabelOrganogramaAtivo->setValue($stOrganogramaAtivo);

# Recupera os dados do Organograma selecionado no filtro.
$stFiltro = "  WHERE  cod_organograma = ".$inCodOrganograma;
$obTOrganogramaOrganograma->recuperaOrganogramas($rsOrganogramaNovo, '', $stFiltro);

# Hidden que guarda o codigo do organograma escolhido na tela de filtro.
$obHdnCodOrganograma = new Hidden;
$obHdnCodOrganograma->setId   ('inCodOrganograma');
$obHdnCodOrganograma->setName ('inCodOrganograma');
$obHdnCodOrganograma->setValue($inCodOrganograma);

# Label com o novo organograma
$obLabelOrganogramaNovo = new Label;
$obLabelOrganogramaNovo->setRotulo('Novo Organograma');
$obLabelOrganogramaNovo->setValue($inCodOrganograma." - ".$rsOrganogramaNovo->getCampo('implantacao'));

# Definição do Formulário
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnCodOrganograma);
$obFormulario->addComponente($obLabelOrganogramaAtivo);
$obFormulario->addComponente($obLabelOrganogramaNovo);

$obTConfigurarMigracaoOrganogramaDinamico = new TConfigurarMigracaoOrganogramaDinamico;

# Faz a carga do De-Para.
$obTConfigurarMigracaoOrganogramaDinamico->recuperaCargaDeParaOrganograma($rsCargaDePara);

# Monta o RecordSet com o Organograma Padrão (antigo).
$obTConfigurarMigracaoOrganogramaDinamico->recuperaOrganogramaUtilizado($rsOrganogramaUtilizado);

# Monta o RecordSet com todos os órgãos do novo Organograma.
$obTConfigurarMigracaoOrganogramaDinamico->setDado('cod_organograma', $inCodOrganograma);
$obTConfigurarMigracaoOrganogramaDinamico->recuperaFuturoOrgao($rsListaOrgaos);

# Monta a(s) tabela(s) para configuração do De-Para.
while (!$rsOrganogramaUtilizado->eof()) {

    $inCodOrganogramaUtilizado = $rsOrganogramaUtilizado->getCampo('cod_organograma');

    $obSpan = new Span;
    $obSpan->setId('spnTableOrganograma_'.$inCodOrganogramaUtilizado);

    $obFormulario->addSpan($obSpan);

    # Monta o RecordSet com o Organograma Padrão (antigo).
    $obTConfigurarMigracaoOrganogramaDinamico = new TConfigurarMigracaoOrganogramaDinamico;
    $obTConfigurarMigracaoOrganogramaDinamico->setDado('cod_organograma', $inCodOrganogramaUtilizado);
    $obTConfigurarMigracaoOrganogramaDinamico->recuperaOrganogramaAntigo($rsOrganogramaAntigo);

    # Validação do array da sessão de órgãos (usada para guardar os valores quando a paginaçao for acionada).
    while (!$rsOrganogramaAntigo->eof()) {

        $inCodOrgao = $rsOrganogramaAntigo->getCampo('cod_orgao');
        $stNewValue = $rsOrganogramaAntigo->getCampo('cod_orgao_new');

        if (array_key_exists('inCodOrgao_'.$inCodOrgao, $arOrgaoSelecionado)) {
            $rsOrganogramaAntigo->setCampo('cod_orgao_new', $arOrgaoSelecionado['inCodOrgao_'.$inCodOrgao]);
        } elseif (!empty($stNewValue)) {
            $arOrgaoSelecionado['inCodOrgao_'.$inCodOrgao] = $stNewValue;
        }

        $rsOrganogramaAntigo->proximo();
    }

    # Atualiza a sessão com os dados salvos na base.
    Sessao::write('arOrgao', $arOrgaoSelecionado);

    # Codigo utilizado pra atualizar a data do organograma no processamento para a atual
    sessao::write('inCodOrganogramaNovo', $_REQUEST["inCodOrganograma"]);

    sessao::write('inCodOrganogramaAntigo', $rsOrganogramaAtivo->getCampo('cod_organograma'));

    # Seta o ponteiro inicial do RecordSet.
    $rsOrganogramaAntigo->setPrimeiroElemento();

    # Definição do Header da Lista.
    $stSummary = "Organograma: ".$rsOrganogramaUtilizado->getCampo('cod_organograma')." - Data de Implantação: ".$rsOrganogramaUtilizado->getCampo('implantacao');

    $obLista = new Lista;
    $obLista->setId('table_'.$inCodOrganogramaUtilizado);
    $obLista->setTitulo($stSummary);
    $obLista->setRecordSet($rsOrganogramaAntigo);

    # Se retornar mais de 10 registros, adiciona o recurso de paginação.
    $obLista->setMostraPaginacao((($rsOrganogramaAntigo->getNumLinhas() > 10) ? true : false));

    # INÍCIO - Cabeçalho
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Seq.");
    $obLista->ultimoCabecalho->setWidth(5);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Organograma Antigo");
    $obLista->ultimoCabecalho->setWidth(50);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Novo Organograma");
    $obLista->ultimoCabecalho->setWidth(50);
    $obLista->commitCabecalho();
    # FIM - Cabeçalho

    # INÍCIO - Dados
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[orgao_reduzido] - [descricao]" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obCmbOrganograma = new Select;
    $obCmbOrganograma->setName      (      'inCodOrgao_[cod_orgao]_'          );
    $obCmbOrganograma->setValue     (      '[cod_orgao_new]'                  );
    $obCmbOrganograma->setCampoId   (      '[cod_orgao]'                      );
    $obCmbOrganograma->setCampoDesc (      '[orgao] - [descricao]'            );
    $obCmbOrganograma->setStyle     (      'width:350px; height: 25px;'       );
    $obCmbOrganograma->addOption    (      'null', 'Selecione', 'selected'    );
    $obCmbOrganograma->obEvento->setOnChange("ajaxJavaScript('OCConfigurarMigracaoOrganogramaDinamico.php?&stName='+this.name+'&stValue='+this.value,'atualizaArrayOrgao');");
    $obCmbOrganograma->preencheCombo($rsListaOrgaos);

    # Adiciona o componente Select com todos os órgãos disponíveis.
    $obLista->addDadoComponente($obCmbOrganograma);
    $obLista->commitDadoComponente();
    # FIM - Dados

    # Prepara o HTML da Lista.
    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n", "", $stHTML);
    $stHTML = str_replace( "  ", "", $stHTML);
    $stHTML = str_replace( "'" , "\\'", $stHTML);

    # Monta a tabela para a migração do Organograma.
    $stJsTable .= "jq('#spnTableOrganograma_".$inCodOrganogramaUtilizado."').html('".$stHTML."'); \n";

    $rsOrganogramaUtilizado->proximo();
}

# Monta a visualização do formulário.
$obFormulario->Cancelar($pgFilt);
$obFormulario->show();

# Faz a requisição para montar a tabela da migração.
$stJs  = "<script type='text/javascript'> \n";
$stJs .= $stJsTable;
$stJs .= "</script>                       \n";

echo $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
