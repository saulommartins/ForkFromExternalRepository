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
    * Página oculto para Relatório Definível de Servidor
    * Data de Criação   : 05/03/2006

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-03 11:26:12 -0300 (Qui, 03 Abr 2008) $

    * Casos de uso: uc-04.04.48
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_PES_COMPONENTES.'IFiltroTipoFolha.class.php';
include_once CAM_GRH_FOL_COMPONENTES.'IBscEvento.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'DefinivelServidor';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    return $stJs;
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanAtivosAposentados()
{
    $stSituacao = $_GET['stSituacao'];

    $stJs .= limparSpans();

    include_once CAM_GRH_PES_COMPONENTES.'IFiltroCompetencia.class.php';
    $stOnChange = "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=false','gerarSpanTipoFolha' );";
    $obIFiltroCompetencia = new IFiltroCompetencia(true,'',true);
    $obIFiltroCompetencia->obCmbMes->obEvento->setOnChange($stOnChange);
    $obIFiltroCompetencia->obTxtAno->obEvento->setOnChange($stOnChange);

    include_once CAM_GRH_PES_COMPONENTES.'IFiltroComponentes.class.php';
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoServidor();
    $obIFiltroComponentes->setRegimeSubDivisaoFuncao();
    $obIFiltroComponentes->setFuncao();
    $obIFiltroComponentes->setCargo();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoRegimeSubDivisaoFuncao();
    $obIFiltroComponentes->setGrupoAtributoServidor();
    $obIFiltroComponentes->setGrupoFuncao();
    $obIFiltroComponentes->setGrupoCargo();

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'ativos':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindidos':
                $obFormulario->addTitulo('Rescindidos');
                $obIFiltroComponentes->setRescisao();
            break;
    }

    $obIFiltroCompetencia->geraFormulario($obFormulario);
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $stEval = $obFormulario->getInnerJavaScript();
    $obFormulario->montaInnerHTML();
    $stHtml  = $obFormulario->getHTML();
    $stHtml .= gerarSpanComboCampos();
    $stHtmlBotoes = gerarSpanBotoes();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= "d.getElementById('spnBotoes').innerHTML = '$stHtmlBotoes';\n";
    $stJs .= "f.hdnTipoFiltroExtra.value = '$stEval';\n";

    return $stJs;
}

###########################PENSIONISTAS#####################################

function gerarSpanPensionistas()
{
    $stSituacao = $_GET['stSituacao'];

    $stJs .= limparSpans();

    include_once CAM_GRH_PES_COMPONENTES.'IFiltroCompetencia.class.php';
    $stOnChange = "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroTipoFolha.php?".Sessao::getId()."&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=false','gerarSpanTipoFolha' );";
    $obIFiltroCompetencia = new IFiltroCompetencia(true,'',true);
    $obIFiltroCompetencia->obCmbMes->obEvento->setOnChange($stOnChange);
    $obIFiltroCompetencia->obTxtAno->obEvento->setOnChange($stOnChange);

    include_once CAM_GRH_PES_COMPONENTES.'IFiltroComponentes.class.php';
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatriculaPensionista();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoPensionista();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoAtributoPensionista();

    $obFormulario = new Formulario();
    $obFormulario->addTitulo('Pensionistas');
    $obIFiltroCompetencia->geraFormulario($obFormulario);
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml  = $obFormulario->getHTML();
    $stHtml .= gerarSpanComboCampos();
    $stHtmlBotoes = gerarSpanBotoes();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= "d.getElementById('spnBotoes').innerHTML = '$stHtmlBotoes';\n";

    return $stJs;
}

function gerarSpanComboCampos()
{
    $stSituacao = $_GET['stSituacao'];

    $inOrdem = 0;
    $arListaCampos = array(
    'Matrícula'                 =>'20,'.$inOrdem++,
    'Nome'                      =>'90,'.$inOrdem++,
    'CPF'                       =>'25,'.$inOrdem++,
    'RG'                        =>'25,'.$inOrdem++,
    'PIS/PASEP'                 =>'30,'.$inOrdem++,
    'Data Nascimento'           =>'25,'.$inOrdem++,
    'Escolaridade'              =>'30,'.$inOrdem++,
    'Endereço'                  =>'75,'.$inOrdem++,
    'Bairro'                    =>'35,'.$inOrdem++,
    'CEP'                       =>'20,'.$inOrdem++,
    'Fone'                      =>'35,'.$inOrdem++,
    'Município'                 =>'45,'.$inOrdem++,
    'UF'                        =>'10,'.$inOrdem++,
    'Título de Eleitor'         =>'20,'.$inOrdem++,
    'Seção do Título'           =>'20,'.$inOrdem++,
    'Zona Título'               =>'24,'.$inOrdem++,
    'CTPS'                      =>'20,'.$inOrdem++,
    'Série CTPS'                =>'24,'.$inOrdem++,
    'Data Nomeação'             =>'25,'.$inOrdem++,
    'Data Posse'                =>'25,'.$inOrdem++,
    'Data Admissão'             =>'25,'.$inOrdem++,
    'Data Rescisão'             =>'25,'.$inOrdem++,
    'Causa Rescisão'            =>'40,'.$inOrdem++,
    'Regime/Subdivisão Cargo'   =>'50,'.$inOrdem++,
    'Cargo/Especialidade'       =>'50,'.$inOrdem++,
    'Regime/Subdivisão Função'  =>'50,'.$inOrdem++,
    'Função/Especialidade'      =>'50,'.$inOrdem++,
    'Categoria'                 =>'35,'.$inOrdem++,
    'Tipo Admissão'             =>'35,'.$inOrdem++,
    'Vínculo Empregatício'      =>'35,'.$inOrdem++,
    'Classif Agentes Nocivos'   =>'50,'.$inOrdem++,
    'Horas Mensais'             =>'20,'.$inOrdem++,
    'Horas Semanais'            =>'20,'.$inOrdem++,
    'Padrão'                    =>'30,'.$inOrdem++,
    'Valor Padrão'              =>'30,'.$inOrdem++,
    'Salário'                   =>'30,'.$inOrdem++,
    'Forma Pagamento'           =>'35,'.$inOrdem++,
    'Banco'                     =>'30,'.$inOrdem++,
    'Agência'                   =>'30,'.$inOrdem++,
    'Conta'                     =>'30,'.$inOrdem++,
    'Lotação'                   =>'45,'.$inOrdem++,
    'Local'                     =>'40,'.$inOrdem++,
    'Previdência'               =>'35,'.$inOrdem++,
    'Data Opção FGTS'           =>'25,'.$inOrdem++,
    'Salário Bruto'             =>'30,'.$inOrdem++,
    'Salário Líquido'           =>'30,'.$inOrdem++,
    'Descontos da Folha Salário'=>'30,'.$inOrdem++,
    'Evento1 Quantidade'        =>'20,'.$inOrdem++,
    'Evento1 Valor'             =>'30,'.$inOrdem++,
    'Evento2 Quantidade'        =>'20,'.$inOrdem++,
    'Evento2 Valor'             =>'30,'.$inOrdem++,
    'Evento3 Quantidade'        =>'20,'.$inOrdem++,
    'Evento3 Valor'             =>'30,'.$inOrdem++,
    'Evento4 Quantidade'        =>'20,'.$inOrdem++,
    'Evento4 Valor'             =>'30,'.$inOrdem++
    );

    if ($stSituacao == 'pensionistas') {

        $inOrdem = 0;
        $arListaCampos = array(
            'Matrícula'                   =>'20,'.$inOrdem++,
            'Nome'                        =>'90,'.$inOrdem++,
            'CPF'                         =>'25,'.$inOrdem++,
            'RG'                          =>'25,'.$inOrdem++,
            'Data Nascimento'             =>'30,'.$inOrdem++,
            'Escolaridade'                =>'35,'.$inOrdem++,
            'Endereço'                    =>'75,'.$inOrdem++,
            'Bairro'                      =>'35,'.$inOrdem++,
            'CEP'                         =>'25,'.$inOrdem++,
            'Fone'                        =>'35,'.$inOrdem++,
            'Município'                   =>'45,'.$inOrdem++,
            'UF'                          =>'10,'.$inOrdem++,
            'Banco'                       =>'35,'.$inOrdem++,
            'Agência'                     =>'35,'.$inOrdem++,
            'Conta'                       =>'30,'.$inOrdem++,
            'Lotação'                     =>'45,'.$inOrdem++,
            'Previdência'                 =>'40,'.$inOrdem++,
            'CID'                         =>'40,'.$inOrdem++,
            'Número Benefício'            =>'25,'.$inOrdem++,
            'Matrícula Gerador Benefício' =>'30,'.$inOrdem++,
            'Nome Gerador Benefício'      =>'75,'.$inOrdem++,
            'Ocupação'                    =>'50,'.$inOrdem++,
            'Grau Parentesco'             =>'50,'.$inOrdem++,
            'Tipo Dependência'            =>'40,'.$inOrdem++,
            'Percentual Pagamento Pensão' =>'30,'.$inOrdem++,
            'Data Início Benefício'       =>'30,'.$inOrdem++,
            'Processo'                    =>'25,'.$inOrdem++,
            'Data Inclusão Processo'      =>'30,'.$inOrdem++,
            'Data Encerramento Benefício' =>'30,'.$inOrdem++,
            'Motivo Encerramento'         =>'45,'.$inOrdem++,
            'Salário Bruto'               =>'30,'.$inOrdem++,
            'Salário Líquido'             =>'30,'.$inOrdem++,
            'Descontos da Folha Salário'  =>'30,'.$inOrdem++,
            'Evento1 Quantidade'          =>'20,'.$inOrdem++,
            'Evento1 Valor'               =>'30,'.$inOrdem++,
            'Evento2 Quantidade'          =>'20,'.$inOrdem++,
            'Evento2 Valor'               =>'30,'.$inOrdem++,
            'Evento3 Quantidade'          =>'20,'.$inOrdem++,
            'Evento3 Valor'               =>'30,'.$inOrdem++,
            'Evento4 Quantidade'          =>'20,'.$inOrdem++,
            'Evento4 Valor'               =>'30,'.$inOrdem++
            );
    }

    include_once CAM_GA_ADM_NEGOCIO.'RCadastroDinamico.class.php';
    $obRCadastroDinamico = new RCadastroDinamico();
    $obRCadastroDinamico->setCodCadastro(5);
    if ($stSituacao == 'pensionistas') {
        $obRCadastroDinamico->setCodCadastro(7);
    }
    $obRCadastroDinamico->obRModulo->setCodModulo(22);
    $obRCadastroDinamico->recuperaAtributos($rsAtributos);

    $contadorOrdemChaves = 100;
    while (!$rsAtributos->eof()) {
        $stNomAtributo = $rsAtributos->getCampo('nom_atributo');
        if (strlen($stNomAtributo) <= 23) {
            $inTamanho = 50;
        } elseif (strlen($stNomAtributo) > 23 AND strlen($stNomAtributo) <= 28) {
            $inTamanho = 55;
        } elseif (strlen($stNomAtributo) > 28 AND strlen($stNomAtributo) <= 32) {
            $inTamanho = 60;
        } elseif (strlen($stNomAtributo) > 32 AND strlen($stNomAtributo) <= 36) {
            $inTamanho = 65;
        } else {
            $inTamanho = 70;
        }

        $arListaCampos[$rsAtributos->getCampo('nom_atributo')] = $inTamanho.','.$contadorOrdemChaves;
        $rsAtributos->proximo();
        $contadorOrdemChaves++;
    }

    ksort($arListaCampos);
    Sessao::write('arListaCampos', $arListaCampos);

    $obCmbCampos = new Select;
    $obCmbCampos->setRotulo                                 ('Campos');
    $obCmbCampos->setTitle                                  ('Selecione o campo que deseja listar no relatório e clique em incluir.');
    $obCmbCampos->setName                                   ('stCampo');
    $obCmbCampos->setId                                     ('stCampo');
    $obCmbCampos->setStyle                                  ('width: 200px');
    $obCmbCampos->addOption                                 ('', 'Selecione');
    foreach ($arListaCampos as $stLabel=>$inPontos) {
        $obCmbCampos->addOption                             ($stLabel, $stLabel);
    }
    $obCmbCampos->setNullBarra(false);
    $obCmbCampos->obEvento->setOnChange("montaParametrosGET('verificarCampo', 'stCampo');");

    $obFormulario = new Formulario();
    $obFormulario->addTitulo     ('Lista de Campos à Imprimir no Relatório');
    $obFormulario->addComponente ($obCmbCampos);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    return $stHtml;
}

function gerarSpanBotoes()
{
    //// FORMULARIO
    $obBtnLimparCampos = new Ok;
    $obBtnLimparCampos->setValue('Limpar');
    $obBtnLimparCampos->obEvento->setOnClick("executaFuncaoAjax('limparFormularioCampos');");

    $obBtnOK = new Ok;
    $obBtnOK->setValue('Incluir');
    $obBtnOK->obEvento->setOnClick("montaParametrosGET('incluirCampo','stCampo, inCodigoEvento, hdnDescEvento, stDataInicial, stDataFinal', true);");

    $botoesForm  = array ($obBtnOK , $obBtnLimparCampos);

    $obFormulario = new Formulario();
    $obFormulario->defineBarra($botoesForm);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    return $stHtml;
}

function organizaArrayLista()
{
    $cont = 0;
    $arRetorno = array();

    $arCampos = Sessao::read('arCampos');
    ksort($arCampos);

    if (count($arCampos) > 0) {
        foreach ($arCampos as $chave => $dados) {
            $arRetorno[$cont] = $dados;
            $cont++;
        }
    }
    Sessao::write('arCampos', $arCampos);

    return $arRetorno;
}

function incluirCampo()
{
    $obErro = new Erro();
    $arCampos = Sessao::read('arCampos');
    $arCamposValores = array('Salário Bruto', 'Salário Líquido', 'Descontos da Folha Salário');
    $arListaCampos = Sessao::read('arListaCampos');
    $stCampo = $_GET['stCampo'];
    $stCampoDescricao = $_GET['stCampo'];
    $inCodCampoEventoSelecionado = substr($stCampoDescricao, 6, 1);
    $inCodCampoEventoProximo = 1;
    $inCodUltimoCampoEvento = 1;
    $boTipoFolha = false;

    if ($stCampo == '') {
        return;
    }

    if (Sessao::read('pontos') >= 220) {
        $obErro->setDescricao('Número de campos relacionados na lista excede o limite disponível para impressão.');
    }

    list($tamanho, $ordem) = explode(',', $arListaCampos[$stCampo]);
    if (!$obErro->ocorreu()) {
        if (count($arCampos) > 0) {
            foreach ($arCampos as $arCampo) {
                if (preg_match('/evento/', strtolower($arCampo['campo']))  || in_array($stCampo, $arCamposValores)) {
                    $inCodCampoEventoUltimo = substr($arCampo['campo'], 6, 1);
                    // Vai substituindo até o último elemento
                    $inCodCampoEventoProximo = $inCodCampoEventoUltimo + 1;
                    $boTipoFolha = true;
                }

                if ($arCampo['ordem'] == $ordem) {
                    $obErro->setDescricao('O campo '.$stCampo.' já está incluído na lista.');
                }
            }
        }
    }

    if (preg_match('/evento/', strtolower($stCampo))) {
        if ($_GET['inCodigoEvento'] == '') {
            $obErro->setDescricao('Informe qual o evento a ser demonstrado no relatório.');
        }

        // Verifica se o usuário seguiu a sequência na numeração dos campos de eventos
        if ($inCodCampoEventoSelecionado > $inCodCampoEventoProximo) {
            $obErro->setDescricao('Não é possível incluir o Evento '.$inCodCampoEventoSelecionado.' sem antes incluir o Evento '.$inCodCampoEventoProximo.'!');
        }

        $stEvento = $_GET['inCodigoEvento'].' - '.$_GET['hdnDescEvento'];
        $stCampoDescricao = $stCampo.' / '.$stEvento;
        $arCampo['cod_evento'] = $_GET['inCodigoEvento'];
        $arCampo['nom_evento'] = $_GET['hdnDescEvento'];
    }

    if (preg_match('/data/', strtolower($stCampo))) {
        $pattern = "((1[0-2])|(0[1-9]))"; //Expressão regular para pegar o mês exato entre 01-09 ou 10-12

        /*
            O IF tem que verificar exatamente se os campos inicial e final são o mesmo para garantir que foi adicionado corretamente
            e ainda testar se ambos inicial e final estão dentro da expressão regular determinada anteriormente
        */
        if (($_REQUEST['stDataInicial'] == $_REQUEST['stDataFinal']) &&
            (preg_match($pattern,$_REQUEST['stDataInicial']) == 1)   &&
            (preg_match($pattern,$_REQUEST['stDataFinal']) == 1)
           ) {
            $inMes = (int)$_REQUEST['stDataInicial']; //convertendo o string do mês para garantir que irá corretamente
            $stCampoDescricao = $stCampo.' ('.SistemaLegado::mesExtensoBR($inMes).') ';
            $arCampo['stMes'] = $_GET['stDataInicial'];
        } else {
            $stCampoDescricao = $stCampo.' (' .$_REQUEST['stDataInicial']. ' - '. $_REQUEST['stDataFinal']. ') ';

            if ($_GET['stDataInicial'] != '' || $_GET['stDataFinal'] != '') {
                $arCampo['stDataInicial'] = $_GET['stDataInicial'];
                $arCampo['stDataFinal'] = $_GET['stDataFinal'];
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        include_once CAM_GRH_PES_NEGOCIO.'RPessoalServidor.class.php';
        $arPontos = Sessao::read('pontos');

        $arCampo['ordem']          = $ordem;
        $arCampo['campo']          = $stCampo;
        $arCampo['campoDescricao'] = $stCampoDescricao;
        $arCampo['tamanho']        = $tamanho;

        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        if ($_REQUEST['stSituacao'] == 'pensionistas') {
            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setCodCadastro(7);
        }
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        while (!$rsAtributos->eof()) {
            if ($rsAtributos->getCampo('nom_atributo') == $stCampo) {
                $arCampo['codAtributo'] = $rsAtributos->getCampo('cod_atributo');
            }
            $rsAtributos->proximo();
        }

        $arCampos[$ordem] = $arCampo;
        Sessao::write('arCampos', $arCampos);
        $stJs = montaListaCampos();
        $stJs .= limparFormularioCampos();
        $arPontos += $arListaCampos[$stCampo];
        Sessao::write('pontos', $arPontos);

        if (preg_match('/evento/', strtolower($stCampo)) && !$boTipoFolha) {
            $obIFiltroTipoFolha = new IFiltroTipoFolha();
            $obIFiltroTipoFolha->setValorPadrao('1');

            $obFormularioTipoFolha = new Formulario;
            $obIFiltroTipoFolha->geraFormulario($obFormularioTipoFolha);
            $obFormularioTipoFolha->montaInnerHtml();

            $stHtmlTipoFolha = $obFormularioTipoFolha->getHTML();

            $stJs .= "d.getElementById('spnFiltroTipoFolha').innerHTML = '".$stHtmlTipoFolha."'; ";
        }
    }

    return $stJs;
}

function limparCampos()
{
    Sessao::remove('arCampos');
    Sessao::remove('pontos');

    $stJs .= "d.getElementById('spnListaCampos').innerHTML = '';     	   \n";

    return $stJs;
}

function limparFormularioCampos()
{
    $stJs .= "d.getElementById('stCampo').selectedIndex = 0\n";
    $stJs .= "d.getElementById('spnCampos').innerHTML = ''; ";

    return $stJs;
}

function excluirCampo()
{
    $arTemp   = array();
    $arCampos = Sessao::read('arCampos');
    $arPontos = Sessao::read('pontos');
    $arListaCampos = Sessao::read('arListaCampos');
    $boErro = false;

    $boTipoFolha = false;
    foreach ($arCampos as $arCampo) {
        if ($arCampo['ordem'] != $_GET['ordem']) {
            $arTemp[] = $arCampo;
            $stCampoTMP = $arCampo['campo'];
            $arCamposTMP = array('Salário Bruto', 'Salário Líquido', 'Descontos da Folha Salário');
            if (preg_match('/evento/', strtolower($stCampoTMP)) || in_array($stCampoTMP, $arCamposTMP)) {
                $boTipoFolha = true;
            }
        } else {
            $stCampo = $arCampo['campo'];
        }
    }

    if ($boErro === false) {
        Sessao::write('arCampos', $arTemp);
        $arPontos -= $arListaCampos[$stCampo];
        Sessao::write('pontos', $arPontos);
    }

    if ($boTipoFolha === false) {
        $stJs .= "d.getElementById('spnFiltroTipoFolha').innerHTML = ''; ";
    }

    $stJs .= montaListaCampos();

    return $stJs;
}

function montaListaCampos()
{
    $rsCampos = new RecordSet();
    $rsCampos->preenche(organizaArrayLista());

    $obLista = new Lista;
    $obLista->setTitulo          ( 'Campos do Relatório'     );
    $obLista->setMostraPaginacao ( false                     );
    $obLista->setRecordset       ( $rsCampos                 );

    // Cabeçalho da lista
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( '&nbsp;'        );
    $obLista->ultimoCabecalho->setWidth    ( 3               );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( 'Nome do Campo' );
    $obLista->ultimoCabecalho->setWidth    ( 75              );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( 'Ordenar Por'   );
    $obLista->ultimoCabecalho->setWidth    ( 75              );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( 'Ação'          );
    $obLista->ultimoCabecalho->setWidth    ( 5               );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( 'campoDescricao' 	     );
    $obLista->commitDado();

    $obChkOrdenar = new CheckBox();
    $obChkOrdenar->setName('boOrdenar_[campo]_');
    $obChkOrdenar->setTitle('Clique para ordenar a lista por este campo.');

    $obLista->addDadoComponente   ($obChkOrdenar);
    $obLista->ultimoDado->setCampo('booleano');
    $obLista->commitDadoComponente();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao      ('EXCLUIR');
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink      ("JavaScript:executaFuncaoAjax('excluirCampo');");
    $obLista->ultimaAcao->addCampo     ('1', 'ordem');
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnListaCampos').innerHTML = ' " .$stHtml. "'; ";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();
    if (count(Sessao::read('arCampos')) == 0) {
        $obErro->setDescricao('Selecionar pelo menos um campo na lista para imprimir o relatório.');
    }
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs = "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

function verificarCampo()
{
    $stCampo = $_REQUEST['stCampo'];
    if (preg_match('/evento/', strtolower($stCampo))) {
        $obIBscEvento = new IBscEvento();
        $obIBscEvento->setTodosEventos(true);
        $obIBscEvento->obBscInnerEvento->setObrigatorio(true);

        $obFormulario = new Formulario;
        $obIBscEvento->geraFormulario($obFormulario);
        $obFormulario->montaInnerHtml();

        $stHtml = $obFormulario->getHTML();

        $stJs = "d.getElementById('spnCampos').innerHTML = '".$stHtml."'; ";
    } else

    if (preg_match('/data/', strtolower($stCampo))) {

        $obPeriodicidade = new Periodicidade();
        $obPeriodicidade->setRotulo("Periodicidade");
        $obPeriodicidade->setTitle("Informe a Periodicidade.");
        $obPeriodicidade->setExercicio(Sessao::getExercicio());
        $obPeriodicidade->setAnoVazio(true); //Necessário para poder usar o parâmetro do mês sem informar o exercicío
        $obPeriodicidade->setValue(4);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obPeriodicidade);

        $obFormulario->montaInnerHtml();

        $stHtml = $obFormulario->getHTML();

        $stJs = "d.getElementById('spnCampos').innerHTML = '".$stHtml."'; ";
    } else
        $stJs = "d.getElementById('spnCampos').innerHTML = ''; ";

    return $stJs;
}

function dropTmpTable()
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;

    $stSql = " DROP TABLE ".$_REQUEST['stNomeTabela'];
    $obErro = $obConexao->__executaDML( $stSql, $boTransacao );

    if ($obErro->ocorreu()) {
        $stJs = "alertaAviso('@Erro ao excluir tabela temporária!','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case 'incluirCampo':
        $stJs = incluirCampo();
        break;
    case 'limparFormularioCampos':
        $stJs = limparFormularioCampos();
        break;
    case 'excluirCampo':
        $stJs = excluirCampo();
        break;
    case 'submeter':
        $stJs = submeter();
        break;
    case 'limparCampos':
        $stJs = limparCampos();
        break;
    case 'gerarSpanAtivosAposentados':
        $stJs .= gerarSpanAtivosAposentados();
        break;
    case 'gerarSpanPensionistas':
        $stJs .= gerarSpanPensionistas();
        break;
    case 'verificarCampo':
        $stJs .= verificarCampo();
        break;
    case 'dropTmpTable':
        $stJs .= dropTmpTable();
}

if ($stJs) {
    echo $stJs;
}
?>
