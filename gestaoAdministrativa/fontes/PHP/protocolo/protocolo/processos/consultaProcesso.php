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

    $Id: consultaProcesso.php 65625 2016-06-02 18:34:54Z jean $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."processosLegado.class.php"; //Insere a classe que manipula os dados do processo
include_once CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include_once 'interfaceProcessos.class.php'; //Inclui classe que contém a interface html
include_once CAM_FW_LEGADO."botoesPdfLegado.class.php"; //Classe para geração dos botões de criação de relatório
include_once CAM_FW_LEGADO."paginacaoLegada.class.php";
include_once CAM_FW_LEGADO."mascarasLegado.lib.php";

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";

setAjuda('uc-01.06.98');

if (is_null($request->get('ctrl'))) {
    $ctrl = 0;
} else {
    $ctrl = $request->get('ctrl');
}

$codProcesso     = $request->get('codProcesso');
$anoExercicio    = $request->get('anoExercicio');
$pagina          = $request->get('pagina');
$mostraDados     = $request->get('mostraDados');
$mostraEmApenso  = $request->get('mostraEmApenso');
$mostraApensado  = $request->get('mostraApensado');
$stChaveProcesso = $request->get('stChaveProcesso');
$numCgm          = $request->get('numCgm');
$voltar          = $request->get('voltar');
$verificador     = $request->get('verificador');
$controle        = $request->get('controle');

$codProcessoFl    = $request->get('codProcessoFl');
$codClassificacao = $request->get('codClassificacao');
$codAssunto       = $request->get('codAssunto');
$numCgmU          = $request->get('numCgmU');
$numCgmUltimo     = $request->get('numCgmUltimo');
$dataInicial      = $request->get('dataInicial');
$dataFinal        = $request->get('dataFinal');

?>
<script type="text/javascript">

     function zebra(id, classe)
     {
            var tabela = document.getElementById(id);
            var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
                ((i%2) == 0) ? linhas[i].className = classe : void(0);
            }
    }
    //Valida se os campos da pesquisa foram preenchidos.
    function ValidaCampos()
    {
    var boComFiltro = false;

    for (var i = 0; i < document.frm.elements.length; i++) {

        if (document.frm.elements[i].type == "text") {
            if (document.frm.elements[i].value != "") {
                boComFiltro = true;
            }
        }
        if (document.frm.elements[i].type == "checkbox") {
            if (document.frm.elements[i].checked) {
                boComFiltro = true;
            }
        }
        if (document.frm.elements[i].type == "select") {
           if ( ( document.frm.elements[i].value != "xxx" ) || ( document.frm.elements[i].value != "0" )) {
                boComFiltro = true;
           }
        }

    }

    return boComFiltro;
    }

function preencheComZerosProcesso(mascara, campo, posicao, exercicio, tamanhocod, tamanho)
{
    var expReg = new RegExp("[a-zA-Z0-9]","ig");
    var stComplemento   = '';
    var inInicio  = 0;
    var inFim     = 0;
    var sub       = "";
    var sub1      = "";
    var sub2      = "";
    var sub3      = "";
    //posicao == 'E' || posicao == 'D'
    if (posicao == 'E') {
        inInicio  = 0;
        inFim     = mascara.length - campo.value.length;
    } else {
        inInicio  = campo.value.length;
        inFim     = mascara.length;
    }

    for (var inCount=inInicio; inCount<inFim; inCount++) {
        if ( mascara.charAt(inCount).search(expReg) == -1 ) {
            stComplemento = stComplemento + mascara.charAt(inCount);
        } else {
            stComplemento = stComplemento + '0';
        }
    }
tamanhocod = parseInt(tamanhocod , 10) + 1;

    if (posicao == 'E') {
        sub = document.frm.codProcesso.value.substring(tamanhocod , tamanho);
        sub2 = document.frm.codProcesso.value.substring(0, tamanhocod );
        sub3 = document.frm.codProcesso.value.substring(0, parseInt(tamanhocod , 10) - 1);
        sub1 = sub.length;
            campo.clear;
            if (sub1 == 3) {
                campo.value = stComplemento + sub2 + "0" + sub;
            }
            if (sub1 == 2) {
                campo.value = stComplemento + sub2 + "00" + sub;
            }
            if (sub1 == 1) {
                campo.value = stComplemento + sub2 + "000" + sub;
            }
            if (sub1 == 0) {
                campo.value = stComplemento + sub3 +"/"+  exercicio;
            }
    } else {
        campo.value = campo.value + stComplemento;
    }
}

//compara a data 1 com a data 2
//retorna 1 => data 1 maior que data 2
//        0 => datas iguais
//       -1 => data 1 menor que data 2
//        2 => numero errado de digitos
//hora => coloca a hora na comparacao
function compareDateTime(valDate1, valDate2, bhora)
{
    dateMask = "E";
    if (bhora == true) {
        if (valDate1.length < 18 || valDate2.length < 18) {
            return 2;
        }
    } else {
        if (valDate1.length < 10 || valDate2.length < 10) {
            return 2;
        }
    }
    var dia = "";
    var mes = "";
    var ano = "";
    var hora = "";
    var minuto1 = "";
    var minuto = "";
    if (bhora == true) {
        hora = valDate1.substring(13, 15);
        minuto = valDate1.substring(15);
        minuto1 = minuto;
    }
    if (dateMask == 'U') { //Americano
        mes = valDate1.substring(0, 2);
        dia = valDate1.substring(3, 5);
        ano = valDate1.substring(6, 10);
    } else if (dateMask == 'E') { //Europeu
        dia = valDate1.substring(0, 2);
        mes = valDate1.substring(3, 5);
        ano = valDate1.substring(6, 10);
   } else { //Geral
        ano = valDate1.substring(0, 4);
        mes = valDate1.substring(5, 7);
        dia = valDate1.substring(8, 10);
    }
    var somaTotal1 = parseInt(ano+mes+dia, 10);
    if (bhora == true) {
        somaTotal1 = parseInt(ano+mes+dia+hora+minuto, 10);
    }
    if (bhora == true) {
        hora = valDate2.substring(13, 15);
        minuto = valDate2.substring(15);
    }
    if (dateMask == 'U') { //Americano
        mes = valDate2.substring(0, 2);
        dia = valDate2.substring(3, 5);
        ano = valDate2.substring(6, 10);
    } else if (dateMask == 'E') { //Europeu
        dia = valDate2.substring(0, 2);
        mes = valDate2.substring(3, 5);
        ano = valDate2.substring(6, 10);
    } else { //Geral
        ano = valDate2.substring(0, 4);
        mes = valDate2.substring(5, 7);
        dia = valDate2.substring(8, 10);
    }
    var somaTotal2 = parseInt(ano+mes+dia, 10);
    if (bhora == true) {
        somaTotal2 = parseInt(ano+mes+dia+hora+minuto, 10);
    }

    somaTotal1 = somaTotal1+minuto1;
    somaTotal2 = somaTotal2+minuto;

    if (somaTotal1 == somaTotal2) {
        return 0;
    } else if (somaTotal1 > somaTotal2) {
        return 1;
    } else {
        return -1;
    }
}

//A função Valida() faz a verfificação dos campos, monte-a conforme a sua necessidade.
    function ValidaData()
    {
        var mensagem = "";
        var erro = false;
        var dataI;
        var dataF;

        dataI = document.frm.dataInicial.value;
        dataF = document.frm.dataFinal.value;

    //Não executar a busca sem que haja pelo menos um parâmetro informado
    if ( compareDateTime(dataI, dataF, false) == 1 ) {
        mensagem += "@Data inicial não pode ser maior que a data final.";
        erro = true;
    }
    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return !(erro);
    }

function Valida()
{
        var mensagem = "";
        var erro = false;
        var campo1;
        var campo2;
        var campo3;
        var campo4;
        var campo5;
        var campo6;
        var campo7;
        var campo8;
        var campo9;
        var campo10;
        var campo11;
<?php

  $selectSSE =  "SELECT
                       cod_situacao,
                       nom_situacao
                   FROM
                       sw_situacao_processo
                   ORDER BY
                       nom_situacao";

       $dbConfigSE = new databaseLegado;
       $dbConfigSE->abreBd();
       $dbConfigSE->abreSelecao($selectSSE);
       while (!$dbConfigSE->eof()) {
           $codSSE = $dbConfigSE->pegaCampo("cod_situacao");
           $listaSSE[$codSSE] =$dbConfigSE->pegaCampo("nom_situacao");
           $dbConfigSE->vaiProximo();
       }
       $dbConfigSE->limpaSelecao();
       $dbConfigSE->fechaBd();
       if ($listaSSE != "") {
           $iE = 11;
           while (list($keySE, $valSE) = each($listaSSE)) {
           $iE = $iE +1;
           echo "var campo$iE; ";
           }
       }
   ?>

        var campoaux;

        campo1 = document.frm.codProcesso.value.length;
        campo2 = document.frm.resumo.value.length;
        campo3 = document.frm.numCgm.value.length;
        campo4 = document.frm.dataInicial.value.length;
        campo5 = document.frm.dataFinal.value.length;
        campo6 = document.frm.codClassificacao.value;
        campo7 = document.frm.codAssunto.value;
        campo8 = document.frm.codOrgao.value;
        campo9 = document.frm.codUnidade.value;
        campo10 = document.frm.codDepartamento.value;
        campo11 = document.frm.codSetor.value;

<?php  $selectSS =  "SELECT
                       cod_situacao,
                       nom_situacao
                   FROM
                       sw_situacao_processo
                   ORDER BY
                       nom_situacao";

       $dbConfigS = new databaseLegado;
       $dbConfigS->abreBd();
       $dbConfigS->abreSelecao($selectSS);
       while (!$dbConfigS->eof()) {
           $codSS = $dbConfigS->pegaCampo("cod_situacao");
           $listaSS[$codSS] =$dbConfigS->pegaCampo("nom_situacao");
           $dbConfigS->vaiProximo();
       }
       $dbConfigS->limpaSelecao();
       $dbConfigS->fechaBd();
       if ($listaSS != "") {
           $i = 11;
           while (list($keyS, $valS) = each($listaSS)) {
           $i = $i +1;
           echo "campo$i = document.frm.codSituacao_".$keyS.".checked; \n";
           }
       }

        //BUSCA OS ATRIBUTOS DE PROCESSO
        //IMLEMTAÇÃO FEITAEM FUNÇÃO A IMPORTAÇÃO DE SISTEMAS LEGADOS DAS PREF
        $stSQL  = "  SELECT                                                                     \n";
        $stSQL .= "      sw_atributo_protocolo.*                                                \n";
        $stSQL .= "      ,sw_processo_atributo.indexavel                                        \n";
        $stSQL .= "  FROM                                                                       \n";
        $stSQL .= "      sw_processo_atributo,                                                  \n";
        $stSQL .= "      sw_atributo_protocolo                                                  \n";
        $stSQL .= "  WHERE                                                                      \n";
        $stSQL .= "      sw_processo_atributo.cod_atributo=sw_atributo_protocolo.cod_atributo   \n";
        $stSQL .= "      AND sw_processo_atributo.indexavel='t'                                 \n";
        $stSQL .= "  ORDER BY cod_atributo                                                      \n";

        $dbAtribProc = new databaseLegado;
        $dbAtribProc->abreBd();
        $dbAtribProc->abreSelecao($stSQL);

        if ($dbAtribProc->numeroDeLinhas > 0) {
            while ( !$dbAtribProc->eof() ) {
                echo "if ( document.frm.valorAtributo_".$dbAtribProc->pegaCampo("cod_atributo").".value!='' ) {\n";
                echo "    erro = false;\n";
                echo "}\n";
                $dbAtribProc->vaiProximo();
            }
        }

        // BUSCA OS ATRIBUTOS DE ASSUNTO DE PROCESSO
        $stSelect ="SELECT * FROM sw_atributo_protocolo";
        $dbAtribAssunto = new dataBaseLegado;
        $dbAtribAssunto->abreBd();
        $dbAtribAssunto->abreSelecao($stSelect);

        if ($dbAtribAssunto->numeroDeLinhas > 0) {
            while (!($dbAtribAssunto->eof())) {
                $nomAtributo = $dbAtribAssunto->pegaCampo("nom_atributo");
                $tipo        = $dbAtribAssunto->pegaCampo("tipo");
                $valorLista  = $dbAtribAssunto->pegaCampo("valor_padrao");

                if ($tipo == "l") {
                    $lista = explode("\n", $valorLista);
                    $numValor = $dbAtribAssunto->pegaCampo("valor_padrao");
                    $listaTipoCmb = explode("\n", $tipo);
                }
                if ($tipo == "t") {
                    $stTexto = $dbAtribAssunto->pegaCampo("valor_padrao");
                    $listaTipoTxt = explode("\n", $tipo);
                }
                if ($tipo == "n") {
                    $numNumero = $dbAtribAssunto->pegaCampo("valor_padrao");
                    $listaTipoNum = explode("\n", $tipo);
                }
                $dbAtribAssunto->vaiProximo();
            }
        }

?>

        if ( document.frm.dataInicial.value.length > 0 && !verificaData(document.frm.dataInicial) ) {
            mensagem += "@Campo Período de inclusão inválido!("+document.frm.dataInicial.value+")";
            erro = true;
        }

        if ( document.frm.dataFinal.value.length > 0 && !verificaData(document.frm.dataFinal) ) {
            mensagem += "@Campo Período de inclusão inválido!(" + document.frm.dataFinal.value + ")";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return !(erro);
    }

    function Envia()
    {
        if (Valida()) {
           if (ValidaData()) {
               document.frm.action = "consultaProcesso.php?<?=Sessao::getId()?>&ctrl=1";
               document.frm.submit();
           }
        }
    }
    function ImprimeEtiqueta()
    {
        document.frm.action = "imprimirEtiqueta.php?<?=Sessao::getId()?>&ctrl=2&codProcesso<?=$codProcesso?>&anoExercicio<?=$anoExercicio?>&verificador=true";
        document.frm.submit();
    }

    function ImprimeDespachos()
    {
        document.frm.action = "imprimeRelatorioDespachos.php?<?=Sessao::getId()?>&ctrl=2&codProcesso<?=$codProcesso?>&anoExercicio<?=$anoExercicio?>";
        document.frm.submit();
    }

    function ImprimeProcesoArquivado(cod_historico)
    {
        if (cod_historico != '') {
            document.frm.action = "arquivaProcessoDefinitivo.php?<?=Sessao::getId()?>&historicoArquivamento="+cod_historico+" ";
            document.frm.submit();   
        }else{
            document.frm.action = "arquivaProcessoTemporario.php?<?=Sessao::getId()?>";
            document.frm.submit();   
        }
        
    }


    function Salvar()
    {
        document.frm.action = "reciboProcesso.php?<?=Sessao::getId()?>&ctrl=2&codProcesso<?=$codProcesso?>&anoExercicio<?=$anoExercicio?>";
        document.frm.submit();
    }

    function paginando()
    {
        var acao = '<?=Sessao::read('acao')?>';

        if (acao == 58) {
            document.frm.action = "recebeProcesso.php?<?=Sessao::getId()?>&numCgm=<?=$numCgm?>&stChaveProcesso=<?=$stChaveProcesso?>&pagina=<?=$pagina?>&ctrl=2&acao="+acao;
        }

        if (acao == 1623) {
            document.frm.action = "recebeProcessoLote.php?<?=Sessao::getId()?>&numCgm=<?=$numCgm?>&dataInicio=<?=$dataInicial?>&dataTermino=<?=$dataFinal?>&stChaveProcesso=<?=$stChaveProcesso?>&codClassificacao=<?=$codClassificacao?>&pagina=<?=$pagina?>&ctrl=2&acao="+acao;
        }

        if (acao == 61) {
            document.frm.action = "encaminhaProcesso.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&ctrl=2&acao="+acao;
        }

        if (acao == 162) {
            document.frm.action = "imprimeReciboEntrega.php?<?=Sessao::getId()?>&ctrl=0&sOpcao=listaProcessos&pagina=<?=$pagina?>&stChaveProcesso=<?=$stChaveProcesso?>&dataInicio=<?=$dataInicial?>&dataTermino=<?=$dataFinal?>&acao="+acao;
        }

        if (acao == 163) {
            document.frm.action = "cancelaEncaminhamento.php?<?=Sessao::getId()?>&stChaveProcesso=<?=$stChaveProcesso?>&ctrl=1&pagina=<?=$pagina?>&acao="+acao+"&codProcessoFl=<?=$codProcessoFl;?>&codClassificacao=<?=$codClassificacao;?>&codAssunto=<?=$codAssunto;?>&numCgm=<?=$numCgm;?>&numCgmU=<?=$numCgmU;?>&numCgmUltimo=<?=$numCgmUltimo;?>&dataInicio=<?=$dataInicial;?>&dataTermino=<?=$dataFinal;?>&ordem=<?=$ordem;?>";
        }

        if (acao == 59) {
            document.frm.action = "despachaProcesso.php?<?=Sessao::getId()?>&ctrl=1&acao="+acao+"&verifica=true&pagina=<?=$pagina?>&numCgm=<?=$numCgm?>&stChaveProcesso=<?=$stChaveProcesso?>&dataInicio=<?=$dataInicial;?>&dataTermino=<?=$dataFinal;?>";
        }

        if (acao == 127) {
            document.frm.action = "arquivaProcesso.php?<?=Sessao::getId()?>&ctrl=1&acao="+acao+"&verifica=true&pagina=<?=$pagina?>";
        }

        if (acao == 160) {
            document.frm.action = "desarquivaProcesso.php?<?=Sessao::getId()?>&ctrl=1&acao="+acao+"&verifica=true&pagina=<?=$pagina?>";
        }

        if (acao == 324) {
            document.frm.action = "apensaProcesso.php?<?=Sessao::getId()?>&ctrl=2&pagina=<?=$pagina?>&dataInicio=<?=$dataInicial?>&dataTermino=<?=$dataFinal?>&stChaveProcesso=<?=$stChaveProcesso?>";
        }

        if (acao == 318) {
            document.frm.action = "apensaProcesso.php?<?=Sessao::getId()?>&ctrl=2&pagina=<?=$pagina?>";
        }

        if (acao == 319) {
            document.frm.action = "desapensaProcesso.php?<?=Sessao::getId()?>&ctrl=2&pagina=<?=$pagina?>";
        }

        if (acao == 325) {
            document.frm.action = "desapensaProcesso.php?<?=Sessao::getId()?>&ctrl=2&pagina=<?=$pagina?>";
        }

        if (acao == 67) {
            document.frm.action = "consultaProcesso.php?<?=Sessao::getId();?>&ctrl=1&pagina=<?=$pagina;?>&voltar=true";
        }

        document.frm.submit();
    }

    function documentoDigital()
    {
        document.frm.action = "consultaDocumentos.php?<?=Sessao::getId()?>&pagina=<?=$pagina;?>&anoExercicio=<?=$anoExercicio?>&verificador=false";
        document.frm.submit();
    }

    function reciboProcesso()
    {
        document.frm.action="imprimeReciboProcesso.php?<?=Sessao::getId()?>&sAnoExercicio=<?=$anoExercicio?>&iCodProcesso=<?=$codProcesso?>";
        document.frm.submit();
    }

    function DadosInteressado(id)
    {
        document.frm.controle.value = 0;

        mostra = <?=($_GET['mostraDados']) ? '0':'1';?>;
        document.frm.action += "&controle=0&ctrl=2&pagina=<?=$pagina?>&codProcesso=<?=$codProcesso?>&anoExercicio=<?=$anoExercicio?>&verificador=false&mostraEmApenso=<?=$mostraEmApenso;?>&mostraTramite=<?=$mostraTramite;?>&mostraApensado=<?=$mostraApensado;?>&mostraDados="+mostra+"&tdId="+id+"#DadosDoInteressado";
        document.frm.submit();
    }

    function DadosTramites()
    {
        document.frm.controle.value = 0;
        document.frm.action += "&controle=0&ctrl=2&pagina=<?=$pagina?>&codProcesso=<?=$codProcesso?>&anoExercicio=<?=$anoExercicio?>&verificador=false&mostraDados=<?=$mostraDados;?>&mostraEmApenso=<?=$mostraEmApenso;?>&mostraApensado=<?=$mostraApensado;?>&mostraTramite=<?=$_GET['mostraTramite']?'0':'1' ?>#TramitesDoProcesso";
        document.frm.submit();
    }

    function DadosEmApenso()
    {
        document.frm.controle.value = 0;
        document.frm.action += "&controle=0&ctrl=2&pagina=<?=$pagina?>&codProcesso=<?=$codProcesso?>&anoExercicio=<?=$anoExercicio?>&verificador=false&mostraDados=<?=$mostraDados;?>&mostraTramite=<?=$mostraTramite;?>&mostraApensado=<?=$mostraApensado;?>&mostraEmApenso=<?=$_GET['mostraEmApenso']?'0':'1' ?>#EmApensoAProcessos";
        document.frm.submit();
    }

    function DadosApensado()
    {
        document.frm.controle.value = 0;
        document.frm.action += "&controle=0&ctrl=2&pagina=<?=$pagina?>&codProcesso=<?=$codProcesso?>&anoExercicio=<?=$anoExercicio?>&verificador=false&mostraDados=<?=$mostraDados;?>&mostraTramite=<?=$mostraTramite;?>&mostraEmApenso=<?=$mostraEmApenso;?>&mostraApensado=<?=$_GET['mostraApensado']?'0':'1' ?>#ProcessosEmApenso";
        document.frm.submit();
    }
</script>

<?php

if (isset($pagina) && !($verificador)) {
    $ctrl = 1;
}

switch ($ctrl) {
    case 0:
    $anoExercicio = pegaConfiguracao("ano_exercicio");
    $anoExercicioSetor = pegaConfiguracao("ano_exercicio");
?>
    <form name=frm action="consultaProcesso.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&verificador=false" method="post">
    <table width="100%">
        <tr>
            <td class=alt_dados colspan="2">
                Dados para filtro
            </td>
        </tr>
        <tr>
            <td class=label width="30%" title="Número do processo">
                Processo
            </td>
            <td class=field width="70%">
<?php
                $mascaraProcesso = pegaConfiguracao("mascara_processo",5);
                $tamanho = strlen($mascaraProcesso);
                $arr = explode("/", $mascaraProcesso);
                $tamanhocod = strlen($arr[0]);
?>
                <input type="text" name="codProcesso" value="<?=$_POST['codProcesso'];?>"
                   size="<?=$tamanho+1?>" maxlength="<?=$tamanho?>"
                   onBlur="if (this.value != '') preencheComZerosProcesso('<?=$arr[0];?>', this, 'E', '<?=Sessao::getExercicio();?>','<?=$tamanhocod;?>','<?=$tamanho;?>');"
                   onKeyUp="mascaraDinamico('<?=$mascaraProcesso;?>', this, event);">
            </td>
        </tr>
        <?php
            include '../../../framework/legado/filtrosCALegado.inc.php';
            #include("../../../framework/legado/filtrosSELegado.inc.php");

            $obFormulario = new Formulario;
            $obFormulario->setLarguraRotulo(30);
            $obFormulario->addForm(null);

            # Instancia para o novo objeto Organograma
            $obIMontaOrganograma = new IMontaOrganograma(true);

            if (!empty($codOrgaoPadrao))
                $obIMontaOrganograma->setCodOrgao($codOrgaoPadrao);

            $obIMontaOrganograma->geraFormulario($obFormulario);

            $obFormulario->montaHtml();
            echo $obFormulario->getHTML();
        ?>
        </table>
        <table width="100%">
        <tr>
            <td width="30%" class=label title="Situação atual do processo">
                Situação
            </td>
            <td width="70%" class=field>
                <?php
                    $selectS = 	"SELECT
                                    cod_situacao,
                                    nom_situacao
                                FROM
                                    sw_situacao_processo
                                ORDER BY
                                    nom_situacao";
                    $dbConfig = new databaseLegado;
                    $dbConfig->abreBd();
                    $dbConfig->abreSelecao($selectS);
                    while (!$dbConfig->eof()) {
                        $codS = $dbConfig->pegaCampo("cod_situacao");
                        $listaS[$codS] =$dbConfig->pegaCampo("nom_situacao");
                        $dbConfig->vaiProximo();
                    }
                    $dbConfig->limpaSelecao();
                    $dbConfig->fechaBd();
                    if ($listaS != "") {
                        while (list($key, $val) = each($listaS)) {
                            $selected = "";
                            if ($codSituacao == $key) {
                                $selected = "checked";
                            }
                            //echo "<option value=".$key." $selected>".$val."</option>";
                    echo "<input type='checkbox' name='codSituacao[".$key."]' id='codSituacao_".$key."' value=".$key." ".$selected.">".$val."<br>";
                        }
                    }
                ?>
            </td>
        </tr>

        <tr>
            <td class=label title="Interessado pelo processo">
                Interessado
            </td>
            <td class="field" width="60%">
                <input type='hidden' name='HdnnumCgm' value='' >
                <input type='hidden' name='nomCGM' value='' >
                <input type="text" size='8' maxlength='8' onKeyPress="return(isValido(this, event, '0123456789'))" name="numCgm" value='<?=$numCgm;?>' onblur="javascript: document.frm.submit();">
                <?php
                    if ($numCgm != "") {
                        $select = 	"SELECT
                                        nom_cgm
                                    FROM
                                        sw_cgm
                                    WHERE
                                        numcgm = ".$numCgm;
                        $dbConfig = new dataBaseLegado;
                        $dbConfig->abreBd();
                        $dbConfig->abreselecao($select);
                        $nomCgm = htmlspecialchars($dbConfig->pegaCampo("nom_cgm"));
                        $dbConfig->limpaSelecao();
                        $dbConfig->fechaBd();
                    }

                    if ($numCgm != "") {
                        $select = 	"SELECT
                                        j.nom_fantasia
                                    FROM
                                        sw_cgm_pessoa_juridica as j
                                    INNER JOIN
                                        sw_cgm as c
                                    ON
                                        c.numcgm = j.numcgm
                                    AND c.numcgm = ".$numCgm;
                        $dbConfig = new dataBaseLegado;
                        $dbConfig->abreBd();
                        $dbConfig->abreselecao($select);
                        $nomfantasia = htmlspecialchars($dbConfig->pegaCampo("nom_fantasia"));
                        $dbConfig->limpaSelecao();
                        $dbConfig->fechaBd();
                    }

                ?>
                <input type="text" name="nomCgm" size="55" value="<?=$nomCgm;?>"  readonly="">&nbsp;&nbsp;
                <a href='javascript:procurarCgm("frm","numCgm","nomCgm","geral","<?=Sessao::getId()?>");'>
                    <img src="<?=CAM_FW_IMAGENS."procuracgm.gif" ?>" align="absmiddle" alt="Procurar Interessado" width=22 height=22 border=0></a>
                    <?php
                        if ($nomfantasia != "") {
                    ?>
                    <span class='itemText'></span>
                    <tr>
                        <td class="label" width="30%">
                            Nome Fantasia
                        </td>
                        <td class="field">
                            <?=$nomfantasia;?>
                        </td>
                    </tr>

            </td>
        </tr>
                    <?php
                        }
                    ?>
                <tr>
                    <td class='label'>
                        Período de inclusão
                    </td>
                    <td class='field'>
                        <?php geraCampoData("dataInicial", $dataInicial, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');};\"" );?>&nbsp;a&nbsp;
                        <?php geraCampoData("dataFinal", $dataFinal, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');};\"" );?>

                        <!--
                        <input type="text" name="dataInicial" value="<?=$dataInicial;?>" size='10' maxlength=10 readonly="">
                        <a href="javascript:MostraCalendario('frm','dataInicial','<?=Sessao::getId()?>');">
                            <img src="<?=CAM_FW_IMAGENS."calendario.gif" ?>" border=0>
                        </a>
                        &nbsp;
                        <b>a</b>
                        &nbsp;
                            <input type="text" name="dataFinal" value="<?=$dataFinal;?>" size='10' maxlength=10 readonly="">
                            <a href="javascript:MostraCalendario('frm','dataFinal','<?=Sessao::getId()?>');">
                                <img src="<?=CAM_FW_IMAGENS."calendario.gif" ?>" border=0>
                            </a>
                        -->
                    </td>
                </tr>
                <tr>
                    <td class=label>
                        Ordem
                    </td>
                    <td class=field>
                        <select name=ordem>
                            <option value="1">Código / Exercício</option>
                            <option value="2">Nome</option>
                            <option value="3">Classificação / Assunto</option>
                            <option value="4">Data</option>
                        </select>
                    </td>
                </tr>
<?php
        $dbAtribProc->vaiPrimeiro();
        // monta a data de recebimento
       if ($dbAtribProc->numeroDeLinhas > 0) {
?>
            <tr>
                <td class="alt_dados" colspan="2">
                    Atributos de Processo
                </td>
            </tr>
<?php
        while (!$dbAtribProc->eof()) {
?>
                <tr>
                    <td class="label" width="30%">
                        <?=$dbAtribProc->pegaCampo("nom_atributo");?>
                    </td>
                    <td class="field" >
                        <input type='hidden' name='boFlagAtributo' value='true'>
<?php

                $codAtributo = $dbAtribProc->pegaCampo("cod_atributo");
                $nomAtributo = $dbAtribProc->pegaCampo("nom_atributo");
                $tipo        = $dbAtribProc->pegaCampo("tipo");

                if ($tipo == "t") {
                    echo "      <input type='text' name=valorAtributo_".$codAtributo." value=''>";
                }

                if ($tipo == "n") {
                    echo "      <input type='text' name=valorAtributo_".$codAtributo." value='' onKeyPress=return(isValido(this,event,'0123456789'))>";
                }

                if ($tipo == "l") {
                    $valorLista = $dbAtribProc->pegaCampo("valor_padrao");
                    $lista = explode("\n", $valorLista);
                    $selected = "";
                    echo "  <select name='valorAtributo_".$codAtributo."'>\n";
                    while (list($key, $val) = each($lista)) {
                        $val = trim($val);
                        echo "      <option value='".$val."'>".$val."</option>\n";
                    }
                    echo "  </select>\n";
                }
                $dbAtribProc->vaiProximo();
            }

?>
                    </td>
                </tr>
<?php
            $dbConfig->vaiProximo();
        }

        $dbAtribAssunto->vaiPrimeiro();
        if ($dbAtribAssunto->numeroDeLinhas > 0) {
        ?>
            <tr>
                <td class="alt_dados" colspan="2">
                    Atributos de Assunto de Processo
                </td>
            </tr>
        <?php
            while (!$dbAtribAssunto->eof()) {
        ?>
                <tr>
                    <td class="label" width="30%">
                        <?=$dbAtribAssunto->pegaCampo("nom_atributo");?>
                    </td>
                    <td class="field" >
        <?php

                $codAtributo = $dbAtribAssunto->pegaCampo("cod_atributo");
                $nomAtributo = $dbAtribAssunto->pegaCampo("nom_atributo");
                $tipo        = $dbAtribAssunto->pegaCampo("tipo");

                if ($tipo == "t") {
                    echo "      <input type='text' size='60' name=valorAtributoTxt[".$codAtributo."] value=''>";
                }

                if ($tipo == "n") {
                    echo "      <input type='text' size='60' name=valorAtributoNum[".$codAtributo."] value='' onKeyPress=return(isValido(this,event,'0123456789'))>";
                }

                if ($tipo == "l") {
                    $valorLista = $dbAtribAssunto->pegaCampo("valor_padrao");
                    $lista = explode("\n", $valorLista);
                    $selected = "";
                    echo "  <select name='valorAtributoCmb[".$codAtributo."]' style='width: 200px'><option value=''>Selecione</option> \n";
                    while (list($key, $val) = each($lista)) {
                        $val = trim($val);
                        echo "      <option value='".$val."'>".$val."</option>\n";
                    }
                    echo "  </select>\n";
                }

                $dbAtribAssunto->vaiProximo();
            }

?>
            </td>
        </tr>
<?php
    $dbConfig->vaiProximo();
}
?>

                <tr>
                    <td class=field colspan="2">
                        <input type="button" name=ok value="OK" style="width: 60px" onclick="Envia();">&nbsp;
                        <input type="reset" name="limpar" value="Limpar">
                    </td>
                </tr>
            </table>
        </form>
<?php
    break;
    case 1:

        $verificador = false;
        $ok = true;
        $html = "
                <table width=100% id='processos'>
                <tr>
                    <td class='alt_dados' colspan='7'>
                        Registros de processos
                    </td>
                </tr>
                <tr>
                    <td class='labelcenterCabecalho' width='3%'>&nbsp;</td>
                    <td class='labelcenterCabecalho' width='6%' style='vertical-align : middle;'>Código</td>
                    <td class='labelcenterCabecalho' width='50%' style='vertical-align : middle;'>Interessados</td>
                    <td class='labelcenterCabecalho' width='18%' style='vertical-align : middle;'>Classificação</td>
                    <td class='labelcenterCabecalho' width='14%' style='vertical-align : middle;'>Assunto</td>
                    <td class='labelcenterCabecalho' width='6%' style='vertical-align : middle;'>Inclusão</td>
                    <td class='labelcenterCabecalho' width='3%' >&nbsp;</td>
                </tr>
                ";

                if ($boFlagAtributo) {
                    foreach ($_POST AS $stParametro => $stValor) {
                        if (strpos($stParametro,'valorAtributo_')!==false AND trim($stValor)!='') {
                            $inCodAtributo=substr($stParametro,14);
                            $stFiltroAtrib.=" AND EXISTS (                                                                     \n";
                            $stFiltroAtrib.="      SELECT                                                                      \n";
                            $stFiltroAtrib.="          1                                                                       \n";
                            $stFiltroAtrib.="      FROM                                                                        \n";
                            $stFiltroAtrib.="          sw_processo_atributo_valor                                              \n";
                            $stFiltroAtrib.="      WHERE                                                                       \n";
                            $stFiltroAtrib.="         sw_processo.cod_processo  = sw_processo_atributo_valor.cod_processo      \n";
                            $stFiltroAtrib.="         AND sw_processo.ano_exercicio = sw_processo_atributo_valor.ano_exercicio \n";
                            $stFiltroAtrib.="         AND sw_processo_atributo_valor.cod_atributo=".$inCodAtributo."           \n";
                            $stFiltroAtrib.="         AND LOWER(sw_processo_atributo_valor.valor)='".strtolower($stValor)."'   \n";
                            $stFiltroAtrib.=" )";
                        }
                    }
                    if ($stFiltroAtrib) {
                        $stFiltro.=$stFiltroAtrib;
                        unset($stFiltroAtrib);
                    }
                }

$codProcesso = $_REQUEST["codProcesso"];

if ($codProcesso != "") {
    $codProcesso = preg_split("/[\/]/", $codProcesso);
    $stFiltro .= " AND sw_processo.cod_processo  = ".$codProcesso[0]." \n";
}

if ($codProcesso[1] != "") {
    $stFiltro .= " AND sw_processo.ano_exercicio = '".$codProcesso[1]."' \n";
}

// FILTRA PELO ASSUNTO REDUZIDO
$resumo = $_REQUEST["resumo"];
if ($resumo) {
    $resumo = str_replace ("*", "%", $resumo);
    $stFiltro .= " AND resumo_assunto ilike ('%".$resumo."%') \n";
}

$codClassificacao = $_REQUEST["codClassificacao"];
if($codClassificacao!='xxx' && $codClassificacao != "")
    $stFiltro .= " AND sw_classificacao.cod_classificacao = '".$codClassificacao."' \n";

$codAssunto = $_REQUEST["codAssunto"];
if($codAssunto!='xxx' && $codAssunto != "")
    $stFiltro .= " AND sw_processo.cod_assunto = '".$codAssunto."' \n";

$codSituacao = $_REQUEST["codSituacao"];
if (is_array($codSituacao)) {
    $stFiltro .= " AND (";
    $verS =  count($codSituacao);

    $i = 0;
    while (list($key, $val) = each($codSituacao)) {
        $stFiltro .= " sw_situacao_processo.cod_situacao = '".$val."' ";
        if ($i < ($verS - 1)) {
            $stFiltro .= " OR ";
            $i = $i + 1;
        }
    }
    $stFiltro .= ") \n";
}

$numCgm = $_REQUEST["numCgm"];
if(strlen($numCgm)>0)
    $stFiltro .= " AND sw_processo_interessado.numcgm = '".$numCgm."' \n";

if(strlen($nomCgm)>0)
    $stFiltro .= " AND (lower(sw_cgm.nom_cgm) like lower('%".$nomCgm."%')) \n";

$chaveSetor = $codMasSetor;

//Filtra pelo período
$dataInicial = $_REQUEST['dataInicial'];
$dataFinal = $_REQUEST['dataFinal'];

if ( (strlen($dataInicial)>0) and (strlen($dataFinal)==0) ) {
    $dtInicial  = dataToSql($dataInicial);
    $stFiltro  .= " AND sw_processo.timestamp >= '".$dtInicial."' \n";

} elseif ( (strlen($dataInicial)==0) and (strlen($dataFinal)>0) ) {
    $dtFinal = dataToSql($dataFinal);
    $stFiltro .= " AND sw_processo.timestamp<= '".$dtFinal."' \n";

} elseif ( (strlen($dataInicial)>0) and (strlen($dataFinal)>0) ) {
    $dtInicial = dataToSql($dataInicial);
    $dtFinal   = dataToSql($dataFinal)." 23:59:59.999";
    $stFiltro .= " AND sw_processo.timestamp Between '".$dtInicial."' AND '".$dtFinal."' \n";
}

// Filtra pelo Atributo de Assunto
if ($_REQUEST[valorAtributoTxt]) {
    foreach ($_REQUEST[valorAtributoTxt] as $key => $value) {
        if ($_REQUEST[valorAtributoTxt][$key]) {
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.valor ILIKE ( '%".$_REQUEST[valorAtributoTxt][$key]."%' ) ";
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.cod_atributo = '".$key."' \n";
        }
    }
}
if ($_REQUEST[valorAtributoNum]) {
    foreach ($_REQUEST[valorAtributoNum] as $key => $value) {
        if ($_REQUEST[valorAtributoNum][$key]) {
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.valor = '".$_REQUEST[valorAtributoNum][$key]."' ";
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.cod_atributo = '".$key."' \n";
        }
    }
}
if ($_REQUEST[valorAtributoCmb]) {
    foreach ($_REQUEST[valorAtributoCmb] as $key => $value) {
        if ($_REQUEST[valorAtributoCmb][$key]) {
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.valor = '".$_REQUEST[valorAtributoCmb][$key]."' ";
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.cod_atributo = '".$key."' \n";
        }
    }
}

# Filtro pelo cod_orgao.
$inCodOrgao = $_REQUEST['hdnUltimoOrgaoSelecionado'];

# O código abaixo pega o estrutural do orgao e lista todos os cod_orgaos possiveis daquele estrutural
# quando a escolha não for até o ultimo nivel.
if (!empty($inCodOrgao)) {
    $stFiltroAux .= " AND sw_ultimo_andamento.cod_orgao = ".$inCodOrgao." \n";

    if (!empty($_REQUEST['hdninCodOrganograma'])) {

        $stCodigoEstrutural = '';

        $arEstrutural = explode('.', $_REQUEST['hdninCodOrganograma']);
        foreach ($arEstrutural as $key => $value) {
            if ($value != 0)
                $stCodigoEstrutural .= (empty($stCodigoEstrutural) ? $value : ".".$value);
        }

        $stSqlOrgaoPossivelPesquisa = "
                  SELECT  DISTINCT cod_orgao
                    FROM  organograma.orgao_nivel
                   WHERE  publico.fn_mascarareduzida(organograma.fn_consulta_orgao(cod_organograma, cod_orgao)) ILIKE '".$stCodigoEstrutural."%'";

        $dbOrgaoPossivelPesquisa = new dataBaseLegado;
        $dbOrgaoPossivelPesquisa->abreBd();
        $dbOrgaoPossivelPesquisa->abreselecao($stSqlOrgaoPossivelPesquisa);

        $stOrgaoPossivelPesquisa = '';

        while (!$dbOrgaoPossivelPesquisa->eof()) {

            $inCodOrgao = $dbOrgaoPossivelPesquisa->pegaCampo("cod_orgao");
            $stOrgaoPossivelPesquisa .= (empty($stOrgaoPossivelPesquisa) ?  $inCodOrgao : ",".$inCodOrgao);

            $dbOrgaoPossivelPesquisa->vaiProximo();
        }

        $dbOrgaoPossivelPesquisa->limpaSelecao();
        $dbOrgaoPossivelPesquisa->fechaBd();

        $stFiltro .= " AND sw_ultimo_andamento.cod_orgao IN (".$stOrgaoPossivelPesquisa.")";

    } else {
        $stFiltro .= $stFiltroAux;
    }
}

# SQL que realiza a consulta de processos.
$sql = "
      -- CONSULTA QUE RETORNA TODOS OS PROCESSOS NÃO CONFIDENCIAIS.
SELECT      cod_processo
            ,ano_exercicio
            ,array_to_string(array_agg(nom_cgm), ', ') as nom_cgm
            ,nom_classificacao
            ,nom_assunto
            ,timestamp
FROM(
      SELECT
              sw_processo.cod_processo
            , sw_processo.ano_exercicio
            , sw_cgm.nom_cgm
            , sw_classificacao.nom_classificacao
            , sw_assunto.nom_assunto
            , sw_processo.timestamp

        FROM  sw_processo

  INNER JOIN  sw_processo_interessado
          ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo
         AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio

   LEFT JOIN  sw_assunto_atributo_valor
          ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
         AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio

  INNER JOIN  sw_cgm
          ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

  INNER JOIN  sw_assunto
          ON  sw_assunto.cod_assunto = sw_processo.cod_assunto
         AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

  INNER JOIN  sw_classificacao
          ON  sw_classificacao.cod_classificacao = sw_assunto.cod_classificacao

  INNER JOIN  sw_situacao_processo
          ON  sw_situacao_processo.cod_situacao = sw_processo.cod_situacao

  INNER JOIN  sw_ultimo_andamento
          ON  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo
         AND  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio

       WHERE  1=1

         AND  sw_processo.confidencial = 'f'

 ".$stFiltro."

    UNION

      -- SELECT QUE RETORNA QUAL SETOR FOI O RESPONSÁVEL PELO CADASTRO DO PROCESSO
      -- QUANDO O MESMO FOR CONFIDENCIAL.

      SELECT
              sw_processo.cod_processo
            , sw_processo.ano_exercicio
            , sw_cgm.nom_cgm
            , sw_classificacao.nom_classificacao
            , sw_assunto.nom_assunto
            , sw_processo.timestamp

        FROM  sw_processo

  INNER JOIN  (
                  SELECT  MIN(sw_andamento.cod_andamento) as cod_andamento,
                          sw_andamento.cod_processo,
                          sw_andamento.ano_exercicio

                    FROM  sw_andamento

                GROUP BY  sw_andamento.cod_processo,
                          sw_andamento.ano_exercicio

              ) as max_andamento_recebido
          ON  sw_processo.cod_processo  = max_andamento_recebido.cod_processo
         AND  sw_processo.ano_exercicio = max_andamento_recebido.ano_exercicio

  INNER JOIN  sw_andamento
          ON  sw_andamento.cod_processo  = max_andamento_recebido.cod_processo
         AND  sw_andamento.ano_exercicio = max_andamento_recebido.ano_exercicio
         AND  sw_andamento.cod_andamento = max_andamento_recebido.cod_andamento

  INNER JOIN  sw_processo_interessado
          ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo
         AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio

   LEFT JOIN  sw_processo_confidencial
          ON  sw_processo_confidencial.cod_processo = sw_processo.cod_processo
         AND  sw_processo_confidencial.ano_exercicio = sw_processo.ano_exercicio

   LEFT JOIN  sw_assunto_atributo_valor
          ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
         AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio

  INNER JOIN  sw_cgm
          ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

  INNER JOIN  sw_assunto
          ON  sw_assunto.cod_assunto = sw_processo.cod_assunto
         AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

  INNER JOIN  sw_classificacao
          ON  sw_classificacao.cod_classificacao = sw_assunto.cod_classificacao

  INNER JOIN  sw_situacao_processo
          ON  sw_situacao_processo.cod_situacao = sw_processo.cod_situacao

  INNER JOIN  sw_ultimo_andamento
          ON  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo
         AND  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio

       WHERE  1=1

         AND  sw_processo.confidencial = 't'
         AND  (sw_andamento.cod_orgao = ".Sessao::read('codOrgao')." OR  sw_processo_confidencial.numcgm = ".Sessao::read('numCgm').") 

".$stFiltro."

    UNION

      -- SELECT QUE RETORNA EM QUAL SETOR SE ENCONTRA O PROCESSO NAQUELE MOMENTO
      -- QUANDO O MESMO FOR CONFIDENCIAL.

      SELECT  sw_processo.cod_processo
            , sw_processo.ano_exercicio
            , sw_cgm.nom_cgm
            , sw_classificacao.nom_classificacao
            , sw_assunto.nom_assunto
            , sw_processo.timestamp

        FROM  sw_processo

  INNER JOIN  (
                  SELECT  sw_andamento.cod_andamento,
                          sw_andamento.cod_processo,
                          sw_andamento.ano_exercicio

                    FROM  sw_andamento

              INNER JOIN  sw_recebimento
                      ON  sw_andamento.cod_processo  = sw_recebimento.cod_processo
                     AND  sw_andamento.ano_exercicio = sw_recebimento.ano_exercicio
                     AND  sw_andamento.cod_andamento = sw_recebimento.cod_andamento

                GROUP BY  sw_andamento.cod_andamento,
                          sw_andamento.cod_processo,
                          sw_andamento.ano_exercicio

              ) as max_andamento_recebido
          ON  sw_processo.cod_processo  = max_andamento_recebido.cod_processo
         AND  sw_processo.ano_exercicio = max_andamento_recebido.ano_exercicio

  INNER JOIN  sw_andamento
          ON  sw_andamento.cod_processo  = max_andamento_recebido.cod_processo
         AND  sw_andamento.ano_exercicio = max_andamento_recebido.ano_exercicio
         AND  sw_andamento.cod_andamento = max_andamento_recebido.cod_andamento

  INNER JOIN  sw_processo_interessado
          ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo
         AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio

   LEFT JOIN  sw_processo_confidencial
          ON  sw_processo_confidencial.cod_processo = sw_processo.cod_processo
         AND  sw_processo_confidencial.ano_exercicio = sw_processo.ano_exercicio

   LEFT JOIN  sw_assunto_atributo_valor
          ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
         AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio

  INNER JOIN  sw_cgm
          ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

  INNER JOIN  sw_assunto
          ON  sw_assunto.cod_assunto = sw_processo.cod_assunto
         AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

  INNER JOIN  sw_classificacao
          ON  sw_classificacao.cod_classificacao = sw_assunto.cod_classificacao

  INNER JOIN  sw_situacao_processo
          ON  sw_situacao_processo.cod_situacao = sw_processo.cod_situacao

  INNER JOIN  sw_ultimo_andamento
          ON  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo
         AND  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio

       WHERE  1=1

         AND  sw_processo.confidencial = 't'
         AND  sw_andamento.cod_orgao   = ".Sessao::read('codOrgao')."
         AND  (sw_andamento.cod_orgao = ".Sessao::read('codOrgao')." OR  sw_processo_confidencial.numcgm = ".Sessao::read('numCgm').") ";

    $sql .= $stFiltro;

    $sql .= " ) as resultado

            GROUP BY cod_processo
                    ,ano_exercicio
                    ,nom_classificacao
                    ,nom_assunto
                    ,timestamp";

    $st_ordenacao = array(
                            1 => "cod_processo, ano_exercicio",
                            2 => "nom_cgm",
                            3 => "nom_classificacao, nom_assunto",
                            4 => "timestamp");

    if ($_REQUEST["ordem"] != '') {
        Sessao::write('sSQLs',$sql);
        Sessao::write('ordem',$_REQUEST["ordem"]);
    }

    $sSQLs = Sessao::read('sSQLs');

    # Monta o LIMIT + OFFSET para a consulta.
    $offset = 0;

    if ($_REQUEST['pg'] > 1) {
       $offset = ($_REQUEST['pg'] - 1) * 10;
    }

    $stOrderBy = " ORDER BY ".$st_ordenacao[Sessao::read('ordem')]. " ASC LIMIT 10 OFFSET $offset ";
    $sSQLs = $sSQLs.$stOrderBy;

    $obConexao = new Conexao;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $sSQLs, $boTransacao);
    $obErro = $obConexao->executaSQL( $rsRecordSetPaginacao, Sessao::read('sSQLs'), $boTransacao);

    $obPaginacao = new Paginacao;
    $obPaginacao->setRecordSet( $rsRecordSetPaginacao );
    $obPaginacao->geraStrLinks();
    $obPaginacao->geraHrefLinks();
    $obPaginacao->montaHTML();
    
    # Monta a tabela de Paginação 
    $obTabelaPaginacao = new Tabela;
    $obTabelaPaginacao->addLinha();
    $obTabelaPaginacao->ultimaLinha->addCelula();

    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setColSpan( $inNumDados + 2  );
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setClass('show_dados_center_bold');
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->addConteudo("<font size='2'>".$obPaginacao->getHTML()."</font>" );
    $obTabelaPaginacao->ultimaLinha->commitCelula();
    $obTabelaPaginacao->commitLinha();
    $obTabelaPaginacao->addLinha();
    $obTabelaPaginacao->ultimaLinha->addCelula();

    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setColSpan( $inNumDados + 2  );
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setClass('show_dados_center_bold');
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->addConteudo("<font size='2'>Registros encontrados: ".$obPaginacao->getNumeroLinhas()."</font>" );
    $obTabelaPaginacao->ultimaLinha->commitCelula();
    $obTabelaPaginacao->commitLinha();
    $obTabelaPaginacao->montaHTML();

    $stHTMLPaginacao .= $obTabelaPaginacao->getHTML();

    if ($rsRecordSet->getNumLinhas() == 0) {
        $ok = false;
    }

    if ($ok) {
        $count = $obPaginacao->geraContador();
        
        if ($dataInclusaoUltimo == "//") {
            $dataInclusaoUltimo = "&nbsp;";
            $nomUsuarioUltimo   = "&nbsp;";
        }

        $rsRecordSet->setPrimeiroElemento();

        while (!$rsRecordSet->eof()) {

            $codprocesso        = $rsRecordSet->getCampo("cod_processo");
            $anoprocesso        = $rsRecordSet->getCampo("ano_exercicio");
            $processo           = $codprocesso."/".$anoprocesso;
            $assunto            = $rsRecordSet->getCampo("nom_assunto");
            $classificacao      = $rsRecordSet->getCampo("nom_classificacao");
            $dataInclusao       = $rsRecordSet->getCampo("timestamp");

            $arr                = explode(" ", $dataInclusao);
            $arrData            = explode("-", $arr[0]);
            $dataInclusao       = $arrData[2]."/".$arrData[1]."/".$arrData[0];
            $arr2               = explode(" ", $dataInclusaoUltimo);
            $arrData2           = explode("-", $arr2[0]);
            $dataInclusaoUltimo = $arrData2[2]."/".$arrData2[1]."/".$arrData2[0];

            $nomContribuinte    = trim($rsRecordSet->getCampo("nom_cgm"));

            $codProcesso        = explode("/", $processo);

            $mascaraProcesso    = pegaConfiguracao("mascara_processo", 5);
            $codProcessoC       = $codProcesso[0].$codProcesso[1];
            $numCasas           = strlen($mascaraProcesso) - 1;
            $processo           = str_pad($codProcessoC, $numCasas, "0" ,STR_PAD_LEFT);
            $processo           = geraMascaraDinamica($mascaraProcesso, $processo);
            $stBscNomContrib    = $nomContribuinte;
            $stResContribuintes = $stBscNomContrib;

            $html .= "<tr>";
            $html .= "<td class=show_dados_center_bold>".$count++."</td>\n";
            $html .= "<td class=show_dados>".$processo."</td>\n";
            $html .= "<td class=show_dados>".$stResContribuintes."</td>\n";
            $html .= "<td class=show_dados>".$classificacao."</td>\n";
            $html .= "<td class=show_dados>".$assunto."</td>\n";
            $html .= "<td class=show_dados>".$dataInclusao."</td>\n";
            $html .= "<td class=botao><a
            href='consultaProcesso.php?".Sessao::getId()."&ctrl=2&pagina=".$pagina."&codProcesso=".$codProcesso[0]."&anoExercicio=".$anoprocesso."&verificador=true'>
            <img src='".CAM_FW_IMAGENS."procuracgm.gif' align='absmiddle' title='Consultar processo' border=0></a></td>\n";

            $rsRecordSet->proximo();
        }

         //redireciona!
        if ($voltar == false) {
            if ($rsRecordSet->numeroDeLinhas==1) {
                $url =
                "consultaProcesso.php?".Sessao::getId()."&ctrl=2&codProcesso=".$codProcesso[0]."&anoExercicio=".$anoprocesso."&verificador=true";
                echo "<script>
                    window.location = \"". $url ."\";
                </script>";
            }
        }
        $html .= "</tr>";
    }
    
    $html .= "</table>";

if (!$ok) { 
    echo "<br><b><span class='itemText'>Nenhum registro encontrado!</span></b><br><br>";
} else {
    echo $html;
    echo "<table id='paginacao' width='850' align='center'>
            <tr>
            <td align='center'>
            <font size=2>";
    echo $stHTMLPaginacao;
    echo "</font></tr></td></table>";
}
    #  * Caso retorne somente uma linha de registro, redireciona para a página com as informações necessárias.
    if ($rsRecordSet->getNumLinhas() == 1) {
        ?>
        <script>
            window.location
        </script>
<?php
    }
?>
    <script>zebra('processos','zb');</script>
<?php
    break;
    case 2:

        $iCodProcesso  = (int) $_REQUEST["codProcesso"];
        $sAnoExercicio = (string) $_REQUEST["anoExercicio"];
        $codProcesso   = $_REQUEST["codProcesso"];
        $anoExercicio  = $_REQUEST["anoExercicio"];

        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), "Processo: ".$iCodProcesso."/".$sAnoExercicio);
        $audicao->insereAuditoria();

        //Verifica se o processo existe
        if (pegaDado("cod_processo","sw_processo","Where cod_processo = '".$codProcesso."' and ano_exercicio = '".$anoExercicio."' ")) {
            $numMatricula = pegaDado("num_matricula","sw_processo_matricula","Where cod_processo = '".$codProcesso."' and ano_exercicio = '".$anoExercicio."' ");
            $numInscricao = pegaDado("num_inscricao","sw_processo_inscricao","Where cod_processo = '".$codProcesso."' and ano_exercicio = '".$anoExercicio."' ");

            $stSQLSetor = "SELECT * FROM SW_ANDAMENTO WHERE (COD_ANDAMENTO = 0 OR COD_ANDAMENTO = 1) AND COD_PROCESSO=".$codProcesso." AND ANO_EXERCICIO = '".$anoExercicio."' ";
            $dbSQLSetor = new databaseLegado;
            $dbSQLSetor->abreBd();
            $dbSQLSetor->abreSelecao($stSQLSetor);
            if (!$dbSQLSetor->eof()) {
                $codOrgao       = $dbSQLSetor->pegaCampo("cod_orgao");
                # $codUnidade     = $dbSQLSetor->pegaCampo("cod_unidade");
                # $codDpto        = $dbSQLSetor->pegaCampo("cod_departamento");
                # $codSetor       = $dbSQLSetor->pegaCampo("cod_setor");
                # $anoExercicioSetor   = $dbSQLSetor->pegaCampo("ano_exercicio_setor");
            }

            $dbSQLSetor->limpaSelecao();
            $dbSQLSetor->fechaBd();
            $mascaraSetor = pegaConfiguracao('mascara_setor',2);
            // mascara o codigo do Setor Inicial

            $codOrgao   = isset($codOrgao)   ? $codOrgao   : null;
            $codUnidade = isset($codUnidade) ? $codUnidade : null;
            $codDpto    = isset($codDpto)    ? $codDpto    : null;
            $codSetor   = isset($codSetor)   ? $codSetor   : null;
            $anoExercicioSetor = isset($anoExercicioSetor) ? $anoExercicioSetor : null;

            $stCodSetor = $codOrgao.".".$codUnidade.".".$codDpto.".".$codSetor."/".$anoExercicioSetor;
            $arCodSetor = validaMascara($mascaraSetor,$stCodSetor);
            // nome do Setor Inicial
            $stFiltroSetor = "where cod_orgao = $codOrgao ";
            # and cod_unidade = $codUnidade and ";
            # $stFiltroSetor .= "cod_departamento = $codDpto and cod_setor = $codSetor and ano_exercicio = '".$anoExercicioSetor."'";
            # $nomSetor = pegaDado("nom_setor","administracao.setor",$stFiltroSetor);

            $p = new processosLegado;
            $andamento = $p->pegaDadosAndamento($codProcesso, $anoExercicio);

            if (is_array($andamento)) {
                foreach ($andamento as $and) {
                    if (empty($max_andamento)) {
                        // seleciona o maior codigo de Andamento
                        $sql_max = "
                            SELECT
                                max(cod_andamento) as max_and
                            FROM
                                sw_andamento
                            WHERE
                                cod_processo = '".$and['codProcesso']."'
                                AND ano_exercicio = '".$and['anoExercicio']."'";

                        $dbConfig = new databaseLegado;
                        $dbConfig->abreBd();
                        $dbConfig->abreSelecao($sql_max);

                        // monta a data de recebimento
                        if ($dbConfig->numeroDeLinhas > 0) {
                            $max_andamento = $dbConfig->pegaCampo("max_and");
                        }
                    }
                }
            }

$sSQL = "";
$sSQL .= "
select p.cod_processo || '/' || p.ano_exercicio as cod_ano_processo,
       c.nom_classificacao, a.nom_assunto, p.timestamp
from sw_processo p, sw_classificacao c, sw_assunto a
where p.cod_processo = $iCodProcesso and
    p.ano_exercicio = '".$sAnoExercicio."' and
    c.cod_classificacao = p.cod_classificacao and
    a.cod_classificacao = p.cod_classificacao and
    a.cod_assunto = p.cod_assunto;";

$sSQL .= "
            SELECT
                p.cod_processo,
            -- COLUNA RETIRADA DA TABELA sw_processo
            -- p.numcgm,
                c.nom_cgm,
                c.logradouro || ', ' || c.numero AS endereco,
                c.bairro,
                c.cep,
                m.nom_municipio ,
                cj.cnpj                          AS cnpjcpf,
                '".$numMatricula."' as nummatricula,
                '".$numInscricao."' as numinscricao
            FROM
                sw_processo                     AS p,
                sw_cgm                          AS c,
                sw_cgm_pessoa_juridica          AS cj,
                sw_municipio                    AS m
            WHERE
                -- cj.numcgm       = p.numcgm        AND
                -- c.numcgm        = p.numcgm        AND
                m.cod_municipio = c.cod_municipio AND
                m.cod_uf        = c.cod_uf        AND
                p.cod_processo  = $iCodProcesso   AND
                p.ano_exercicio = '".$sAnoExercicio."'
            UNION
                SELECT
                    p.cod_processo,
                -- p.numcgm,
                    c.nom_cgm,
                    c.logradouro || ', ' || c.numero AS endereco,
                    c.bairro,
                    c.cep,
                    m.nom_municipio,
                    cf.cpf                           AS cnpjcpf,
                    '".$numMatricula."' as nummatricula,
                    '".$numInscricao."' as numinscricao
                FROM
                    sw_processo                     AS p,
                    sw_cgm                          AS c,
                    sw_cgm_pessoa_fisica            AS cf,
                    sw_municipio                    AS m
                WHERE
                -- cf.numcgm       = p.numcgm        AND
                -- c.numcgm        = p.numcgm        AND
                    m.cod_municipio = c.cod_municipio AND
                    m.cod_uf        = c.cod_uf        AND
                    p.cod_processo  = $iCodProcesso   AND
                    p.ano_exercicio = '".$sAnoExercicio."'
            UNION
                SELECT
                    p.cod_processo,
                --	p.numcgm,
                    c.nom_cgm,
                    c.logradouro || ', ' || c.numero AS endereco,
                    c.bairro,
                    c.cep,
                    m.nom_municipio,
                    'Interno'                        AS cnpjcpf,
                    '".$numMatricula."' as nummatricula,
                    '".$numInscricao."' as numinscricao
                FROM
                    sw_processo                     AS p,
                    sw_cgm                          AS c,
                    sw_municipio                    AS m
                WHERE
                --	c.numcgm        = p.numcgm         AND
                    m.cod_municipio = c.cod_municipio  AND
                    m.cod_uf        = c.cod_uf         AND
                    p.cod_processo  = $iCodProcesso    AND
                    p.ano_exercicio = '".$sAnoExercicio."' AND
                    c.numcgm NOT IN (SELECT
                                        numcgm
                                    FROM
                                        sw_cgm_pessoa_fisica) AND
                    c.numcgm NOT IN (SELECT
                                        numcgm
                                    FROM
                                        sw_cgm_pessoa_juridica);";

// INFORMA SE O PROCESSO FOI RECEBIDO PELO SETOR DE DESTINO
    $sqlCodSituacao = "SELECT cod_situacao FROM sw_processo WHERE cod_processo = ".$_REQUEST['codProcesso']." AND ano_exercicio = '".$_REQUEST['anoExercicio']."' ";
    $dbsqlCodSituacao = new databaseLegado;
    $dbsqlCodSituacao->abreBd();
    $dbsqlCodSituacao->abreSelecao($sqlCodSituacao);
    if (!$dbsqlCodSituacao->eof()) {
        $stCodSituacao = $dbsqlCodSituacao->pegaCampo("cod_situacao");
        if ($stCodSituacao == 3) {
            $stSituacao = '(Recebido)';
        }
    }

    $dbsqlCodSituacao->limpaSelecao();
    $dbsqlCodSituacao->fechaBd();

$nomSetor = isset($nomSetor) ? $nomSetor : null;
$nomSetorFinal = isset($nomSetorFinal) ? $nomSetorFinal : null;
$stSituacao = isset($stSituacao) ? $stSituacao : null;
// INFORMA O SETOR INICIAL E O SETOR DE DESTINO DO PROCESSO
$sSQL .= "
select p.cod_processo, c.nom_classificacao, a.nom_assunto, p.observacoes, p.timestamp, s.nom_situacao,
'".$arCodSetor[1]." - ".$nomSetor."' as setorInicial,
'".$arCodSetorFinal[1]." - ".$nomSetorFinal." ".$stSituacao."' as setorFinal
from sw_processo p, sw_situacao_processo s, sw_classificacao c, sw_assunto a
where p.cod_processo = $iCodProcesso and
    p.ano_exercicio  = '".$sAnoExercicio."' and
    c.cod_classificacao = p.cod_classificacao and
    a.cod_classificacao = p.cod_classificacao and
    a.cod_assunto = p.cod_assunto and
    s.cod_situacao   = p.cod_situacao;";

// ATRIBUTOS DE ASSUNTO DE PROCESSOS
$sSQL .= "
SELECT
    AP.nom_atributo,
    AV.valor
FROM
    sw_atributo_protocolo     AS AP,
    sw_assunto_atributo       AS AT,
    sw_assunto_atributo_valor AS AV,
    sw_processo               AS P
WHERE
    AP.cod_atributo      = AT.cod_atributo      AND
    AT.cod_classificacao = P.cod_classificacao  AND
    AT.cod_assunto       = P.cod_assunto        AND
    AV.cod_atributo      = AT.cod_atributo      AND
    AV.cod_assunto       = AT.cod_assunto       AND
    AV.cod_classificacao = AT.cod_classificacao AND
    AV.cod_processo      = P.cod_processo       AND
    AV.exercicio         = P.ano_exercicio      AND
    P.cod_processo       = '$iCodProcesso'      AND
    P.ano_exercicio      = '".$sAnoExercicio."';";

$sSQL .= "
select dp.cod_processo, d.nom_documento
from sw_documento d, sw_documento_processo dp
where dp.cod_processo = $iCodProcesso and
     dp.exercicio = '$sAnoExercicio' and
      d.cod_documento = dp.cod_documento;";

/*==========================================================================
O Select abaixo foi substituido por: Cleisson Barboza dia 14/04/2005 */
$cod_municipio = pegaConfiguracao("cod_municipio");
$codUf = pegaConfiguracao("cod_uf");
$sSQL .= "
select c.nom_municipio||',' as nom_municipio,
       current_date as hoje
from sw_municipio c
where c.cod_municipio = ".$cod_municipio." and c.cod_uf = ".$codUf.";";
/*
$sSQL .= "
select c.valor||',' as nom_municipio,
       current_date as hoje
from administracao.configuracao c
where c.parametro = 'nom_municipio';";
============================================================================*/

$sSQL .= "
select o.nom_orgao, u.nom_unidade, d.nom_departamento, s.nom_setor
from administracao.orgao as o, administracao.unidade as u, administracao.departamento as d, administracao.setor as s, sw_andamento as a, sw_processo as p
where
o.cod_orgao        = a.cod_orgao        and
o.ano_exercicio    = a.ano_exercicio    and
u.cod_unidade      = a.cod_unidade      and
u.cod_orgao        = o.cod_orgao        and
u.ano_exercicio    = a.ano_exercicio    and
d.cod_departamento = a.cod_departamento and
d.cod_unidade      = u.cod_unidade      and
d.cod_orgao        = o.cod_orgao        and
d.ano_exercicio    = a.ano_exercicio    and
s.cod_setor        = a.cod_setor        and
s.cod_departamento = d.cod_departamento and
s.cod_unidade      = u.cod_unidade      and
s.cod_orgao        = o.cod_orgao        and
s.ano_exercicio    = a.ano_exercicio    and
a.cod_processo     = $iCodProcesso      and
a.timestamp        = p.timestamp;
";

            //Mostra a opção de imprimir ou salvar o relatório
            $sSubTitulo = "Processo número ".$iCodProcesso."/".$sAnoExercicio;
            //$sXML       = CAM_PROTOCOLO.'protocolo/processos/consultaProcesso.xml';
            $sXML = CAM_PROTOCOLO.pegaConfiguracao("caminho_recibo_processo", 5);

            $p = new processosLegado;
            $processo = $p->pegaDados($codProcesso,$anoExercicio);

            $_REQUEST["codClassif"] = $processo["codClassif"];
            $_REQUEST["codAssunto"] = $processo["codAssunto"];
            $_REQUEST['codOrgao']   = $processo["codOrgao"];
            #$_REQUEST['codUnidade'] = $processo["codUnidade"];
            #$_REQUEST['codDpto']    = $processo["codDpto"];
            #$_REQUEST['codSetor']   = $processo["codSetor"];
            #$_REQUEST['anoExercicioSetor'] = $processo["anoExercicioSetor"];

            $botoesPDF  = new botoesPdfLegado;
            
            if ( $processo["codSituacao"] == 5 || $processo["codSituacao"] == 9){                
                
                $stTextComplementar = SistemaLegado::pegaDado("texto_complementar","sw_processo_arquivado","WHERE ano_exercicio = '".$anoExercicio."' AND cod_processo = ".$processo["codProcesso"]."");
                Sessao::write("texto_complementar",$stTextComplementar);

                if ($processo["codSituacao"] == 9) {
                    $historicoArquivamento = SistemaLegado::pegaDado("cod_historico","sw_processo_arquivado","WHERE ano_exercicio = '".$anoExercicio."' AND cod_processo = ".$processo["codProcesso"]." ");
                }                
                $stImprimeProcessoArquivado = '<td class="show_dados" title="Imprimir Carta de Arquivamento de Processo">
                                            <a href="javascript:ImprimeProcesoArquivado('.$historicoArquivamento.');"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a>';
            }else{
                $stImprimeProcessoArquivado = '';
            }

            print '
            <table width="300" cellspacing=0>
                <tr>
                    <td class="show_dados" title="Salvar Relatório">
                    <a href="javascript:Salvar();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                    <td class="show_dados" title="Imprimir Etiqueta">
                    <a href="javascript:ImprimeEtiqueta();"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a>
                    <td class="show_dados" title="Imprimir Despachos do Processo">
                    <a href="javascript:ImprimeDespachos();"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a>                    
                    '.$stImprimeProcessoArquivado.'
                </tr>
            </table>
                ';

            if(!isset($controle)) $controle = 0;
            switch ($controle) {
            case 0:
                //Exibe os dados do processo na tela
                $html2 = new interfaceProcessos;
                $html2->exibeConsultaProcesso($processo);
                $html2->exibeAndamentosProcesso($codProcesso,$anoExercicio, $codSetor);
                break;
            case 1:
                $codSetor = Sessao::read('codOrgao').Sessao::read('codUnidade').Sessao::read('codDpto').Sessao::read('codSetor');
                $html2 = new interfaceProcessos;
                $html2->exibeAndamentosProcesso($codProcesso,$anoExercicio, $codSetor);
                break;
            }

        } else {
            alertaAviso($PHP_SELF.'?'.Sessao::getId(),"Nenhum registro encontrado","unica","aviso");
        }

        break;
        case 100:
            $valor            = $request->get('valor');
            $codClassificacao = $request->get('codClassificacao');
            $codAssunto       = $request->get('codAssunto');
            $codOrgao         = $request->get('codOrgao');
            $nomOrgao         = $request->get('nomOrgao');
            $codDepartamento  = $request->get('codDepartamento');
            $nomDepartamento  = $request->get('nomDepartamento');
            $codUnidade       = $request->get('codUnidade');
            $nomUnidade       = $request->get('nomUnidade');
            $codSetor         = $request->get('codSetor');
            $nomSetor         = $request->get('nomSetor');
            include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
        break;
}//Fim switch
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
