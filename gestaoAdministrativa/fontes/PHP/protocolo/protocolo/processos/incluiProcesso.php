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
    * Arquivo de implementação de manutenção de processo
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.06.98

    $Id: incluiProcesso.php 62581 2015-05-21 14:05:03Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."mascarasLegado.lib.php";
include_once CAM_FW_LEGADO."processosLegado.class.php"; //Classe que manipula os dados do processo
include_once CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include_once 'interfaceProcessos.class.php'; //Inclui classe que contém a interface html
setAjuda('uc-01.06.98');

$controle = $request->get("controle");
$numCgm   = $request->get("numCgm");

$obTransacao = new Transacao();

$codAssunto       = $request->get("codAssunto");
$codClassificacao = $request->get("codClassificacao");
$andamento        = $request->get("andamento");
$nomCGM           = $request->get("nomCGM");
$codAcao   	      = Sessao::read('acao');

if (empty($codAcao)) {
      $codAcao = Sessao::Write('acao',57);
}

if(!isset($controle))
    $controle = 0;

if ($controle > 1 && $controle != 100)
    $controle = 1;

switch ($controle) {
    case 0:
        $html = new interfaceProcessos;
        $html->formIncluiProcesso($_REQUEST,$_SERVER['PHP_SELF']);
    break;

    case 1:

        $arInteressados = Sessao::getRequestProtocolo();

        if (count($arInteressados['interessados']) <= 0) {
            exibeAviso("Informe ao menos um Interessado.","","erro");
            $ok = false;
        } else {

            // Grava o primeiro interessado provisoriamente, esse campo será excluido da tabela,
            // pois foi criado a tabel sw_processo_interessado que possibilita multi-requerentes.
            $numCgm = $arInteressados['interessados'][0]['numCgm'];

            //Verifica se a matrícula informada é válida
            if ($_REQUEST['numMatricula']) {
               include_once( CAM_GT_CIM_MAPEAMENTO."TCIMProprietario.class.php");
               $obTCIMProprietario = new TCIMProprietario();
               $obTCIMProprietario->setDado('inscricao_municipal',$_REQUEST['numMatricula']);
               $obTCIMProprietario->recuperaProprietarioProcesso( $rsRecordSet );
               if ($rsRecordSet->eof()) {
                   exibeAviso("A Inscrição Imobiliária informada não existe!","unica","erro");
                   $okInscricao = false;
               } else {
                $okInscricao = true;
               }
            }

            //Se o andamento foi fornecido pelo usuário, verificar se o setor é válido
            if ($andamento=='outro') {
                if (!validaSetor($chaveSetor,$_REQUEST["anoExercicioSetor"])) {
                    exibeAviso("O setor digitado é inválido!","unica","erro");
                    $okSetor = false;
                }
            }
            //Verifica o tipo de vinculo
            if ($_POST['vinculo'] == 'imobiliaria') {
                if ($okInscricao) {
                    $ok = true;
                } else {
                    $ok = false;
                }
            } else {
                $ok = true;
            }
        }

        if ($_REQUEST['conf'] == 't') {
            if (count($arInteressados['permitidos']) <= 0) {
                exibeAviso("Informe ao menos um CGM que poderá visualizar o processo quando o processo for confidencial.","","erro");
                $ok = false;
            }
        }

        if ($ok) {
            //Verifica o tipo de numeração de processo - manual ou automática
            $tipoNumeracao = pegaConfiguracao("tipo_numeracao_processo",5);
            $anoExercicio = pegaConfiguracao("ano_exercicio");
            if ($tipoNumeracao!=2) { //Não manual
                //pega o próximo código de processo a ser inserido
                $codProcesso = pegaID("cod_processo","sw_processo","Where ano_exercicio = '".$anoExercicio."' ");
            } else {
                $codProcesso = $_REQUEST["codProcesso"];
            }
            //Pega os dados a serem inseridos na tabela de andamento

            if ($andamento == 'padrao') {
                $sql = "SELECT
                                cod_orgao
                          FROM  sw_andamento_padrao
                         WHERE  cod_classificacao = '".$codClassificacao."'
                           AND  cod_assunto = '".$codAssunto."'
                           AND  ordem = '1' ";
                //Chama a classe do banco de dados e executa a query
                $conn = new dataBaseLegado ;
                $conn->abreBD();
                $conn->abreSelecao($sql);
                $conn->vaiPrimeiro();

                $codOrgao          = $conn->pegaCampo("cod_orgao");

                $conn->limpaSelecao();
                $conn->fechaBD();
            } elseif ($andamento=='outro') {
                $vet = explode(".",$chaveSetor);
                $codOrgao = $vet[0];
            }

            # Pega o código do último órgão selecionado, do novo componente do Organograma.
            $codOrgao = (empty($codOrgao) ? $_REQUEST['hdnUltimoOrgaoSelecionado'] : '');

            # Insere o processo no banco de dados e exibe mensagens
            $processos = new processosLegado;
            
            $boProcessoIncluido = $processos->incluiProcesso($codProcesso,$_REQUEST["vinculo"],$codClassificacao,$codAssunto,$numCgm,$_REQUEST["numMatricula"],
                                          $_REQUEST['numInscricao'],$_REQUEST["observacoes"],$_REQUEST["resumo"],$refAnterior,$processosAnexos,
                                          Sessao::read('numCgm'),$codOrgao,$codUnidade,$codDpto,$codSetor,
                                          $anoExercicio,Sessao::read("anoExercicio"),$_REQUEST["codDocumentos"], $_REQUEST["conf"], $_REQUEST["valorAtributo"],
                                          $_REQUEST["codMasSetor"], $arInteressados['interessados'], $arInteressados['permitidos'],$_REQUEST["centroCusto"]);
            if( $boProcessoIncluido ){
                # Insere auditoria
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $codAcao, $codProcesso);
                $audicao->insereAuditoria();

                //Msg: Processo incluido com sucesso! -- Redireciona para a página que emite o recibo
                $observacoes = str_replace(chr(13), "\n", $_REQUEST["observacoes"]);
                $observacoes = str_replace("\n", "/*-", $observacoes);

                $arParametros["iCodProcesso"]     = $codProcesso;
                $arParametros["sAnoExercicio"]    = $anoExercicio;
                $arParametros["vinculo"]          = $_REQUEST["vinculo"];
                $arParametros["codClassif"]       = $codClassificacao;
                $arParametros["codAssunto"]       = $codAssunto;
                $arParametros["numCgm"]           = $numCgm;
                $arParametros["numMatricula"]     = $_REQUEST["numMatricula"];
                $arParametros["numInscricao"]     = $_REQUEST['numInscricao'];
                $arParametros["observacoes"]      = $observacoes;
                $arParametros["codMasSetor"]      = $_REQUEST["codMasSetor"];
                $arParametros["nomCgm"]           = $_REQUEST["nomCGM"];
                $arParametros["nomAssunto"]       = $_REQUEST["nomAssunto"];
                $arParametros["nomClassificacao"] = $_REQUEST["nomClassificacao"];
                $arParametros["codOrgao"]         = $codOrgao;
                $arParametros["centroCusto"]      = $_REQUEST["centroCusto"];

                Sessao::write('arParametros', $arParametros);
                $pag = "imprimeReciboProcesso.php?".Sessao::getId()."&stParametros=sessao&codMasSetor=".$_REQUEST["codMasSetor"]."&anoExercicioSetor=".$anoExercicioSetor."&codProcesso=".$codProcesso."&anoExercicio=".$anoExercicio."&stAcao=Incluir";
                alertaAviso($pag,"Processo ".$codProcesso."/".$anoExercicio,"incluir","aviso", "'.Sessao::getId().'");
            } else {

                if ($tipoNumeracao != 2) { //Não manual
                    exibeAviso($codProcesso."/".$anoExercicio,"n_incluir","erro");
                } else {
                    exibeAviso("Processo ".$codProcesso."/".$anoExercicio." duplicado","n_incluir","erro");
                    $stJs = "f.inCodOrganogramaClassificacao.value = '".$_REQUEST["inCodOrganogramaClassificacao"]."';";
                    $stJs .= " ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/administracao/instancias/processamento/OCIMontaOrganograma.php?".Sessao::getId();
                    $stJs .= "&inCodOrganogramaClassificacao='+jq('#inCodOrganogramaClassificacao').val()+'&hdninCodOrganograma='+jq('#hdninCodOrganograma').val()+'&inNumNiveis=7&stIdOrganograma=inCodOrganograma','preencheCombosOrgaos');";
                    executaFrameOculto($stJs);
                }
                $ok = false;

            }
        }

        if (!$ok) {
            $html = new interfaceProcessos;
            $html->formIncluiProcesso($_REQUEST,$_SERVER['PHP_SELF'],0);
        }

        SistemaLegado::LiberaFrames(true,true);
        
    break;

    case 100:

        $valor            = $_REQUEST['valor'];
        $codClassificacao = $_REQUEST['codClassificacao'];
        $codAssunto       = $_REQUEST['codAssunto'];
        $codOrgao         = $_REQUEST['codOrgao'];
        $nomOrgao         = $_REQUEST['nomOrgao'];
        $codDepartamento  = $_REQUEST['codDepartamento'];
        $nomDepartamento  = $_REQUEST['nomDepartamento'];
        $codUnidade       = $_REQUEST['codUnidade'];
        $nomUnidade       = $_REQUEST['nomUnidade'];
        $codSetor         = $_REQUEST['codSetor'];
        $nomSetor         = $_REQUEST['nomSetor'];

        // para ver se os locais ja estão setados para o usuário
        $codMasTamanho = strlen($valor);
        $arrayCodValor = explode('/', $valor);
        $arCodSetor    = explode('.',$arrayCodValor[0]);

        $aux = preg_split("/[^a-zA-Z0-9]/", $_REQUEST['codClassifAssunto']);

        $codAssunto       = $aux[1];
        $codClassificacao = $aux[0];

        $stJs .= "<script type='text/javascript'>";

        if ((!empty($codClassificacao) && $codClassificacao != 'xxx') &&
            (!empty($codAssunto)       && $codAssunto != 'xxx')){
            $disabled = 'false';
        } else {
            $disabled = 'true';
        }

        $stJs .= " var frame =  window.parent.frames['telaPrincipal']; ";
        $stJs .= " botao = frame.document.getElementById('botaoOk');   ";
        $stJs .= " if (botao) {                                         ";
        $stJs .= "     botao.disabled = ".$disabled.";                 ";
        $stJs .= " }                                                   ";

        $stJs .= "</script>";

        echo $stJs;

        include CAM_FW_LEGADO."filtrosCASELegado.inc.php";

    break;

}//Fim switch

include_once '../../../framework/include/rodape.inc.php'; # Insere o fim da página html

?>
