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
    * Página de Filtro - Exportação Arquivos

    * Data de Criação   : 02/03/2011

    * @author: Tonismar Régis Bernardo

    * @ignore
    * $Id: $
    * $Date: $
    * $Author: $
    * $Rev: $
    *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'Exportacao';
$pgFilt     = 'FL'.$stPrograma.'.php';
$pgList     = 'LS'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgJS       = 'JS'.$stPrograma.'.js';

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('link');

// Usado caso retorne algum erro ao clicar o OK, para poder setar novamente os valores na tela
$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$rsArqExport = $rsAtributos = new RecordSet;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcaoForm = new Hidden;
$obHdnAcaoForm->setName ('hdnPaginaExportacao');
$obHdnAcaoForm->setValue('../../../TCEAM/instancias/exportacao/'.$pgOcul);

// Verificação para saber qual radio deve estar selecionada
$boIndividual = true;
$boCompactado = false;
if ($arFiltroRelatorio['stTipoExport'] == 'compactados') {
    $boIndividual = false;
    $boCompactado = true;
}

// Tipo Arquivo Individual
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ('stTipoExport');
$obRdbTipoExportArqIndividual->setLabel  ('Arquivos Individuais');
$obRdbTipoExportArqIndividual->setValue  ('individuais');
$obRdbTipoExportArqIndividual->setRotulo ('*Tipo de Exportação');
$obRdbTipoExportArqIndividual->setTitle  ('Tipo de Exportação');
$obRdbTipoExportArqIndividual->setChecked($boIndividual);

// Tipo Arquivo Compactado
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName ('stTipoExport');
$obRdbTipoExportArqCompactado->setLabel('Compactados');
$obRdbTipoExportArqCompactado->setValue('compactados');
$obRdbTipoExportArqCompactado->setChecked($boCompactado);

// CadastroContaBancaria provisoriamente alterado para CadastroContas
if ($_REQUEST['stAcao'] == 'orcamento') {
    $arNomeArquivos = array( 'Orcamto.txt'
                            ,'Uniorc.txt'
                            ,'Projat.txt'
                            ,'Itemrec.txt'
                            ,'Dotacao.txt'
                            ,'Programa.txt');
} elseif ($_REQUEST['stAcao'] == 'informes') {
    $arNomeArquivos = array(  'Certidao.txt'
                             ,'Contas.txt'
                             ,'Contrato.txt'
                             ,'Convenio.txt'
                             ,'Movcon.txt'
                             ,'Movdot.txt'
                             ,'Movfun.txt'
                             ,'Ementa.txt'
                             ,'Empenho.txt'
                             ,'Itens.txt'
                             ,'Itemed.txt'
                             ,'Lancada.txt'
                             ,'Licitac.txt'
                             ,'Liquida.txt'
                             ,'Pagamto.txt'
                             ,'Particip.txt'
                             ,'Public.txt'
                             ,'Estorno.txt'
                             ,'Concilia.txt'
                             ,'Diverso.txt'
                             ,'Recibos.txt'
                             ,'Notas.txt'
                             ,'Diarias.txt'
                             ,'Retemp.txt'
                             ,'Folha.txt'
                            );
}  elseif ($_REQUEST['stAcao'] == 'econtas') {
    $arNomeArquivos = array('CONTRATO.REM'
                           ,'CONVENIO.REM'
                           ,'PARTICIPANTECONVENIO.REM'
                           ,'ITEMADESAOATA.REM'
                           ,'ADESAOATALICITACAO.REM'
                           ,'LICITACAO.REM'
                           ,'ITEMLICITACAO.REM'
                           ,'COTACAO.REM'
                           ,'CERTIDAO.REM'
                           ,'PARTICIPANTELICITACAO.REM'
                           ,'CONTRATOEMPENHO.REM'
                           ,'CONVENIOEMPENHO.REM'
                           ,'PUBLICACAO.REM'
                           );
}

for ($inCounter=0;$inCounter < count($arNomeArquivos);$inCounter++) {
    $arElementosArq[$inCounter]['Arquivo'] = $arNomeArquivos[$inCounter];
    $arElementosArq[$inCounter]['Nome'   ] = $arNomeArquivos[$inCounter];
}

// Verificação do select multiplo de arquivos para saber qual já foi selecionado e não deve estar na listagem de Disponivel
// Percorre-se os arquivos selecionados e retira-os da listagem dos disponiveis
$arElementosArqAux = $arElementosArq;
$arArquivosTmp = (isset($arFiltroRelatorio['arArquivosSelecionados']) ? $arFiltroRelatorio['arArquivosSelecionados'] : array());
$arArquivosSelecionados = array();
foreach ($arArquivosTmp as $stArq) {
    foreach ($arElementosArqAux as $inKey => $arDados) {
        if ($arDados['Arquivo'] == $stArq) {
            unset($arElementosArq[$inKey]);
        }
    }
    $arArquivosSelecionados[] = array('Arquivo' => $stArq, 'Nome' => $stArq);
}
sort($arElementosArq);

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();
// Aqui é feita a verificação das entidades selecionadas e atribui-se a compo de entidades o que já havia sido setado
// Além de retirar o que foi setado do select de disponivel
$jsOnLoad = '';
if (isset($arFiltroRelatorio['inCodEntidade'])) {
    foreach ($arFiltroRelatorio['inCodEntidade'] as $inValue) {
        $jsOnLoad .= "\n jq('#inCodEntidade').addOption(".$inValue.", jq('#inCodEntidadeDisponivel option[value=1]').text(), false);";
        $jsOnLoad .= "\n jq('#inCodEntidadeDisponivel').removeOption(".$inValue.");";
    }
}

$obMes = new Mes;
$obMes->setNull(false);

$rsArqSelecionados = new RecordSet;
$rsArqSelecionados->preenche($arArquivosSelecionados);
$rsArqDisponiveis = new RecordSet;
$rsArqDisponiveis->preenche($arElementosArq);

$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName  ('arArquivosSelecionados');
$obCmbArquivos->setRotulo('Arquivos');
$obCmbArquivos->setNull  (false);
$obCmbArquivos->setTitle ('Arquivos Disponiveis');

// lista de ARQUIVOS disponiveis
$obCmbArquivos->SetNomeLista1('arCodArqDisponiveis');
$obCmbArquivos->setCampoId1  ('Arquivo');
$obCmbArquivos->setCampoDesc1('Nome');
$obCmbArquivos->SetRecord1   ($rsArqDisponiveis);

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2('arArquivosSelecionados');
$obCmbArquivos->setCampoId2  ('Arquivo');
$obCmbArquivos->setCampoDesc2('Nome');
$obCmbArquivos->SetRecord2   ($rsArqSelecionados);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction('../../../exportacao/instancias/processamento/PRExportador.php');
$obForm->setTarget('telaPrincipal');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addTitulo        ('Dados para geração de arquivos');
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnAcaoForm);
$obFormulario->addComponente($obISelectEntidade);
if ($_REQUEST['stAcao'] != 'orcamento') {
    $obFormulario->addComponente    ($obMes);
}
$obFormulario->agrupaComponentes(array($obRdbTipoExportArqIndividual, $obRdbTipoExportArqCompactado));
$obFormulario->addComponente    ($obCmbArquivos);

$obFormulario->OK               ();
$obFormulario->show             ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
