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

    $Id: arquivaProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
include (CAM_FW_LEGADO."mascarasLegado.lib.php"     );
setAjuda('uc-01.06.98');

$ctrl             = $_REQUEST['ctrl'];
$stChaveProcesso  = $_REQUEST['stChaveProcesso'];
$codClassificacao = $_REQUEST['codClassificacao'];
$numCgm           = $_REQUEST['numCgm'];
$codAssunto       = $_REQUEST['codAssunto'];
$dataInicio       = $_REQUEST['dataInicio'];
$dataTermino      = $_REQUEST['dataTermino'];
$ordem            = $_REQUEST['ordem'];
$pagina           = $_REQUEST['pagina'];
$arquivamento     = $_REQUEST["arquivamento"];
$codProcesso      = $_REQUEST["codProcesso"];
$anoExercicio     = $_REQUEST["anoExercicio"];
$historicoArquivamento = $_REQUEST["historicoArquivamento"];

$mascaraProcesso = pegaConfiguracao("mascara_processo", 5);
if (!(isset($ctrl)) || trim($ctrl)=="") {
    $ctrl = 0;
}

?>
<script type="text/javascript">
    function Salvar()
    {
        document.frm.action = "arquivaProcesso.php?<?=Sessao::getId()?>&ctrl=1";
        document.frm.submit();
    }
    function zebra(id, classe)
    {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) == 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php

switch ($ctrl) {
case 0:
    $stAuxNome  = "ctrl";
    $stAuxValor = "1";
    include(CAM_FW_LEGADO."filtrosProcessoLegado.inc.php");
break;
case 1:

    if (Sessao::read('vet') != "") {
        $vet = Sessao::read('vet');
        foreach ($vet AS $indice => $valor) {
            $$indice = $valor;
        }
    }
?>
<script type="text/javascript">
    function mudarPag(y)
    {
        window.location.replace(y);
    }
</script>

<?php

    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

    $sql  = "";
    $sql .= "
            SELECT DISTINCT sw_processo.ano_exercicio
                 , sw_processo.cod_processo
                 , sw_processo.timestamp
                 , sw_ultimo_andamento.cod_andamento
                 , sw_classificacao.nom_classificacao
                 , sw_assunto.nom_assunto
                 ,  array_to_string(array_agg(nom_cgm), ', ')as nom_cgm

                 FROM  sw_processo

           INNER JOIN  sw_processo_interessado
                   ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo
                  AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio

           INNER JOIN  sw_assunto
                   ON  sw_assunto.cod_assunto       = sw_processo.cod_assunto
                  AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

           INNER JOIN  sw_classificacao
                   ON  sw_assunto.cod_classificacao = sw_classificacao.cod_classificacao

           INNER JOIN  sw_cgm
                   ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

           INNER JOIN  sw_situacao_processo
                   ON  sw_processo.cod_situacao  = sw_situacao_processo.cod_situacao

           INNER JOIN  sw_ultimo_andamento
                   ON  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio
                  AND  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo

            LEFT JOIN  sw_assunto_atributo_valor
                   ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
                  AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio

                WHERE  sw_situacao_processo.cod_situacao = 3
                  AND  sw_ultimo_andamento.cod_orgao IN (  SELECT cod_orgao
                                                             FROM organograma.vw_orgao_nivel
                                                            WHERE orgao_reduzido LIKE (
                                                                                        SELECT distinct(vw_orgao_nivel.orgao_reduzido)
                                                                                          FROM organograma.vw_orgao_nivel
                                                                                         WHERE vw_orgao_nivel.cod_orgao = ".Sessao::read('codOrgao')."
                                                                                       )";
                                                         # Permissão hierárquica define se o usuário pode ver processos de órgãos em níveis menores ou somente do seu nível.
                                                         $sql .= ($boPermissaoHierarquica == 't') ? "||'%'" : "";
                                                         $sql .= " GROUP BY cod_orgao) ";
            if ($stChaveProcesso != "") {
                $codProcessoFl = preg_split( "/[^a-zA-Z0-9]/", $stChaveProcesso);
                $sql .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
                $vet["stChaveProcesso"] = $stChaveProcesso;
            }
            if ($codProcessoFl[1] != "") {
                $sql .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
                $vet["anoExercicio"]  = $codProcessoFl[1];
            }

            if ($codClassificacao != "" && $codClassificacao != "xxx") {
                $sql .= " AND sw_processo.cod_classificacao = ".$codClassificacao;
                $vet["codClassificacao"] = $codClassificacao;
            }

            if ($codAssunto != "" && $codAssunto != "xxx") {
                $sql .= " AND sw_processo.cod_assunto = ".$codAssunto;
                $vet["codAssunto"] = $codAssunto;
            }

            if ($numCgm != "") {
                $sql .= " AND sw_processo_interessado.numcgm  = ".$numCgm;
                $vet["numCgm"] = $numCgm;
            }

            if ($dataInicio != "" && $dataTermino != "") {
                $arrData     = explode("/", $dataInicio);
                $dataInicioAux = $arrData[2]."-".$arrData[1]."-".$arrData[0];
                $arrData     = explode("/", $dataTermino);
                $dataTerminoAux   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
                $sql .= " AND substr(sw_processo.timestamp::varchar,1,10) >= '".$dataInicioAux."'";
                $sql .= " AND substr(sw_processo.timestamp::varchar,1,10) <= '".$dataTerminoAux."'";
                $vet["dataInicio"] = $dataInicio;
                $vet["dataTermino"]   = $dataTermino;
            }

            //FILTRO POR ATRIBUTO DE ASSUNTO
            if ($_REQUEST['valorAtributoTxt']) {
                foreach ($_REQUEST['valorAtributoTxt'] as $key => $value) {
                    if ($_REQUEST['valorAtributoTxt'][$key]) {
                        $sql .= " AND sw_assunto_atributo_valor.valor ILIKE ( '%".$_REQUEST['valorAtributoTxt'][$key]."%' ) \n";
                        $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                    }
                }
            }
            if ($_REQUEST[valorAtributoNum]) {
                foreach ($_REQUEST[valorAtributoNum] as $key => $value) {
                    if ($_REQUEST[valorAtributoNum][$key]) {
                        $sql .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST[valorAtributoNum][$key]."' \n";
                        $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                    }
                }
            }
            if ($_REQUEST[valorAtributoCmb]) {
                foreach ($_REQUEST[valorAtributoCmb] as $key => $value) {
                    if ($_REQUEST[valorAtributoCmb][$key]) {
                        $sql .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST[valorAtributoCmb][$key]."' \n";
                        $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                    }
                }
            }

            $sql .= " GROUP BY sw_assunto.nom_assunto
                                    ,  sw_assunto.cod_assunto
                                    ,  sw_classificacao.nom_classificacao
                                    ,  sw_classificacao.cod_classificacao
                                    ,  sw_processo.ano_exercicio
                                    ,  sw_processo.cod_processo
                                    ,  sw_processo.timestamp
                                    ,  sw_ultimo_andamento.cod_andamento ";

            Sessao::write('sSQLs',$sql);
            //sessao->transf = $sql;

            $st_ordenacao = array(
                 1 => "sw_processo.ano_exercicio
                     , sw_processo.cod_processo",
                 2 => "sw_cgm.nom_cgm",
                 3 => "sw_classificacao.nom_classificacao
                     , sw_assunto.nom_assunto
                     , sw_processo.ano_exercicio
                     , sw_processo.cod_processo",
                 4 => "sw_processo.timestamp");

            Sessao::write('vet',$vet);
            //sessao->transf5 = $vet;
            if (Sessao::read('ordem') =='') {
            //sessao->transf2 = $ordem;
            Sessao::write('ordem',$ordem);
            }

            if ($ordem=='') {
                //sessao->transf = $sql;
                Sessao::write('sSQLs',$sql);
            }

        include(CAM_FW_LEGADO."paginacaoLegada.class.php");
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento = "&ctrl=1&codProcessoFl=".
        $codProcesso."&codClassificacao=".
        $codClassificacao."&codAssunto=".
        $codAssunto."&numCgm=".
        $numCgm."&dataInicio=".
        $dataInicio."&dataTermino=".
        $dataTermino."&ordem=".
        $ordem;
        $paginacao->geraLinks();
        $paginacao->pegaOrder($st_ordenacao[Sessao::read('ordem')],"ASC");
        $count = $paginacao->contador();
        $sSQL = $paginacao->geraSQL();
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();

        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";

        $exec .= "
        <table width='100%' id='processos'>
            <tr>
                <td class=alt_dados colspan='11'>
                    Registros de processos
                </td>
            </tr>

            <tr>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>&nbsp;</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Código</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Interessado(s)</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Classificação</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Assunto</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Inclusão</td>
                <td class='labelcenterCabecalho'  >&nbsp;</td>
                <td class='labelcenterCabecalho'  >&nbsp;</td>
            </tr>

        ";
        while (!$dbEmp->eof()) {

            $codProcesso      = $dbEmp->pegaCampo("cod_processo");
            $anoE             = $dbEmp->pegaCampo("ano_exercicio");
            $nomClassificacao = $dbEmp->pegaCampo("nom_classificacao");
            $nomAssunto       = $dbEmp->pegaCampo("nom_assunto");
            $interessado      = $dbEmp->pegaCampo("nom_cgm");
            $date              = timestamptobr($dbEmp->pegaCampo("timestamp"));
            $dbEmp->vaiProximo();

            $codProcessoMascara = mascaraProcesso($codProcesso, $anoE);

            $exec .= "
            <tr>
                 <td class='show_dados_center_bold'>
                    ".$count++."
                 </td>
                <td class='show_dados'>
                    ".$codProcessoMascara."
                </td>
                <td class='show_dados'>
                    ".$interessado."
                </td>
                <td class='show_dados'>
                    ".$nomClassificacao."
                </td>
                <td class='show_dados'>
                    ".$nomAssunto."
                </td>
                <td class='show_dados'>
                    ".$date."
                </td>
                <td class='botao'><div align='center' title='Consultar processo'>
                    <a href='consultaProcesso.php?".
                    Sessao::getId()."&codProcesso=".
                    $codProcesso."&anoExercicio=".
                    $anoE."&controle=0&ctrl=2&pagina=".
                    $pagina."&verificador=true'>
                    <img src='".CAM_FW_IMAGENS."procuracgm.gif' alt='Consultar Processo' border=0>
                    </a></div>
                </td>
                <td class=botao title='Arquivar Processo'>
                    <a href='javascript:mudarPag(\"arquivaProcesso.php?".Sessao::getId()."&codProcesso=".$codProcesso."&anoExercicio=".$anoE."&ctrl=2&pagina=".$pagina."\");'>
                        <img src='".CAM_FW_IMAGENS."botao_arquivar.png' border=0>
                    </a>
                </td>
            </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        if ($dbEmp->numeroDeLinhas == 0) {
            $exec .=  "<b>Não Existem Processos a Arquivar!</b>";
        }
        echo "$exec";
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
 <script>zebra('processos','zb');</script>
<?php
break;
case 2:

       $codProcesso = $_REQUEST['codProcesso'];
       $anoExercicio = $_REQUEST['anoExercicio'];

       $sSQL =     "SELECT
                            p.cod_processo,
                            p.ano_exercicio,
                            cl.cod_classificacao,
                            cl.nom_classificacao,
                            ass.cod_assunto,
                            ass.nom_assunto,
                            --c.nom_cgm,
                            u.username as nom_user
                        FROM
                            sw_processo as p,
                            sw_classificacao as cl,
                            sw_assunto as ass,
                            --sw_cgm as c,
                            administracao.usuario as u
                        WHERE
                            p.cod_classificacao   = cl.cod_classificacao AND
                            ass.cod_classificacao = cl.cod_classificacao AND
                            p.cod_assunto         = ass.cod_assunto      AND
                            --p.numcgm              = c.numcgm             AND
                            p.cod_usuario         = u.numcgm             AND
                            p.cod_processo        = ".$codProcesso."     AND
                            p.ano_exercicio       = '".$anoExercicio."' ";

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $codProcesso      = trim($dbEmp->pegaCampo("cod_processo"));
            $anoE             = trim($dbEmp->pegaCampo("ano_exercicio"));
            $codClassificacao = trim($dbEmp->pegaCampo("cod_classificacao"));
            $nomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
            $codAssunto       = trim($dbEmp->pegaCampo("cod_assunto"));
            $nomAssunto       = trim($dbEmp->pegaCampo("nom_assunto"));
            #$nomCgm           = trim($dbEmp->pegaCampo("nom_cgm"));
            $nomUser          = trim($dbEmp->pegaCampo("nom_user"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

?>
<script type="text/javascript">
    //Força um valor máximo para um campo do tipo textarea
    function valorMaximo(campo, limite)
    {
                if (campo.value.length > limite) // se estiver maior que o tamanho estabelecido, reduza-o
                    campo.value = campo.value.substring(0, limite);
            }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f;

        f = document.frm;

        campo = f.historicoArquivamento.value.length;
            if (campo==0) {
                mensagem += "@O campo Motivo do Arquivamento é obrigatório";
                erro = true;
            }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        if (Valida()) {
            document.frm.submit();
        }
    }

    function Cancela()
    {
        document.frm.action = "arquivaProcesso.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&ctrl=1";
        document.frm.submit();
    }
</script>

    <form name="frm" action="arquivaProcesso.php?<?=Sessao::getId();?>&ctrl=3" method="post">
        <table width="100%">

            <tr>
                <td class="alt_dados" colspan=2>
                    Registros de processos
                </td>
            </tr>

            <tr>
                <td class=label width=30%>
                    Código
                </td>
                <td class=field width="70%">
                    <?php
                        $codProcessoMascara = mascaraProcesso($codProcesso, $anoE);
                        echo $codProcessoMascara;
                    ?>
                    <input type="hidden" name="codProcesso" value="<?=$codProcesso;?>">

                </td>
            </tr>
                    <input type="hidden" name="anoE" value="<?=$anoE;?>">
                    <input type="hidden" name="pagina" value="<?=$pagina;?>">

            <tr>
                <td class=label>
                    Classificação/Assunto
                </td>
                <td class=field>
<?php          $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassificacao."-".$codAssunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];
            echo $codClassifAssunto;
?>
<br>
                    <?=$nomClassificacao;?><br>
                    <?=$nomAssunto;?>
                </td>
            </tr>
<?php

        // Busca os interessados da base de dados ( ação de alterar processo )
        $sqlQueryInteressado =
               "SELECT  sw_cgm.nom_cgm, sw_processo_interessado.numcgm
                  FROM  sw_processo_interessado
            INNER JOIN  sw_cgm
                    ON  sw_cgm.numcgm = sw_processo_interessado.numcgm
                 WHERE  sw_processo_interessado.cod_processo = ".$codProcesso."
                   AND  sw_processo_interessado.ano_exercicio = '".$anoE."'";

        $sqlInteressado = new databaseLegado;
        $sqlInteressado->abreBd();
        $sqlInteressado->abreSelecao($sqlQueryInteressado);

        $i = 1;
        while (!$sqlInteressado->eof()) {
            $numCgm = $sqlInteressado->pegaCampo("numcgm");
            $nomCgm = $sqlInteressado->pegaCampo("nom_cgm");

?>
            <tr>
                <td class='label' style='text-align:right;'>Interessado <?=$i;?></td>
                <td class='field'><?=$numCgm;?> - <?=$nomCgm;?></td>
            </tr>
<?php
            $i++;
            $sqlInteressado->vaiProximo();
        }

        $sqlInteressado->limpaSelecao();
        $sqlInteressado->fechaBd();

?>

            <tr>
                <td class=label>
                    Incluido por
                </td>
                <td class=field>
                    <?=$nomUser;?>
                </td>
            </tr>

            <tr>
                <td class=label>
                    *Arquivamento
                </td>
                <td class=field>
                    <select name="arquivamento">
                        <?php
                                $sSQL =     "SELECT
                                                *
                                            FROM
                                                sw_situacao_processo
                                            WHERE
                                                lower(nom_situacao) LIKE lower('%arquiva%')
                                            ORDER BY
                                                nom_situacao";
                                $dbEmp = new dataBaseLegado;
                                $dbEmp->abreBD();
                                $dbEmp->abreSelecao($sSQL);
                                $dbEmp->vaiPrimeiro();
                                $comboProc = "";
                                while (!$dbEmp->eof()) {
                                    $codSituacao  = trim($dbEmp->pegaCampo("cod_situacao"));
                                    $nomSituacao  = trim($dbEmp->pegaCampo("nom_situacao"));
                                    $dbEmp->vaiProximo();
                                    $comboProc .= "<option value=".$codSituacao.">".$nomSituacao."</option>\n";
                                }
                            $dbEmp->limpaSelecao();
                            $dbEmp->fechaBD();
                            echo $comboProc;
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class=label>
                    *Motivo do Arquivamento
                </td>
                <td class=field>
                    <select name="historicoArquivamento">
                        <?php
                                $sSQL =     "SELECT
                                                *
                                            FROM
                                                sw_historico_arquivamento
                                            ORDER BY
                                                nom_historico";
                                $dbEmp = new dataBaseLegado;
                                $dbEmp->abreBD();
                                $dbEmp->abreSelecao($sSQL);
                                $dbEmp->vaiPrimeiro();
                                $combohis = "";
                                while (!$dbEmp->eof()) {
                                    $codHis  = trim($dbEmp->pegaCampo("cod_historico"));
                                    $nomHis  = trim($dbEmp->pegaCampo("nom_historico"));
                                    $dbEmp->vaiProximo();
                                    $combohis .= "<option value=".$codHis.">".$nomHis."</option>\n";
                            }
                            $dbEmp->limpaSelecao();
                            $dbEmp->fechaBD();
                            echo $combohis;
                        ?>
                    </select>
                </td>
            </tr>

            <!-- Localização Física -->
            <tr>
                <td class=label>
                    Localização Física do Arquivamento
                </td>
                <td class=field>
                    <input type="text" id="localizacaoFisica" name="localizacaoFisica" size="80"></textarea>
                </td>
            </tr>

            <tr>
                <td class=label>
                    Texto complementar
                </td>
                <td class=field>
                    <textarea name="textoComplementar" cols=40 rows=6></textarea>
                </td>
            </tr>

            <tr>
                <td colspan='2' class='field'>
                    <?php geraBotaoOk(1,1,1,1); ?>
                </td>
            </tr>
        </table>
    </form>

<?php
break;

case 3:

    include '../situacaoProcesso.class.php';
    $textoComplementar = $_REQUEST['textoComplementar'];
    Sessao::write('texto_complementar',$textoComplementar);

    $codProcesso           = $_REQUEST['codProcesso'];
    $arquivamento          = $_REQUEST['arquivamento'];
    $historicoArquivamento = $_REQUEST['historicoArquivamento'];
    $anoE                  = $_REQUEST['anoE'];
    $stLocalizacaoFisica   = $_REQUEST['localizacaoFisica'];

    $situacaoProcesso = new situacaoProcesso;
    $situacaoProcesso->setaVariaveisArquivamento($arquivamento,$codProcesso,$historicoArquivamento,$anoE,$stLocalizacaoFisica);

    if ($situacaoProcesso->insereArquivamento()) {
        include(CAM_FW_LEGADO."auditoriaLegada.class.php");
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codProcesso.'/'.$anoE);
        $audicao->insereAuditoria();
        echo '<script type="text/javascript">
        alertaAviso("Processo '.$codProcesso.'/'.$anoE.' arquivado com sucesso","unica","aviso", "'.Sessao::getId().'");
        window.location="arquivaProcesso.php?'.Sessao::getId().'&ctrl=4&codProcesso='.$codProcesso.'&anoExercicio='.$anoE.'&arquivamento='.$arquivamento.'&historicoArquivamento='.$historicoArquivamento.'";
        </script>';
    } else {
        echo '<script type="text/javascript">
        alertaAviso("Não foi possível arquivar o Processo '.$codProcesso.'/'.$anoE.'","unica","erro", "'.Sessao::getId().'");
        window.location = "arquivaProcesso.php?'.Sessao::getId().'&ctrl=1&codProcessoFl='.
        $codProcessoFl.'&codClassificacao='.
        $codClassificacao.'&codAssunto='.
        $codAssunto.'&numCgm='.
        $numCgm.'&numCgmU='.
        $numCgmU.'&numCgmUltimo='.
        $numCgmUltimo.'&dataInicial='.
        $dataInicial.'&dataFinal='.
        $dataFinal.'&ordem='.
        $ordem.'";
        </script>';
    }

break;

case 4:
//echo $arquivamento;
//*************************
// Direciona conforme o arquivamento
// se 5 é Arquivado temporário, se é 9 é Arquivado definitivo
//*************************

switch ($arquivamento) {
case 5:
//#########################

?>
    <script type="text/javascript">
        function SalvarTemporario()
        {
            document.frm.action = "arquivaProcessoTemporario.php?<?=Sessao::getId()?>&ctrl=4&arquivamento=5&codProcesso=<?=$codProcesso?>&anoExercicio=<?=$anoExercicio?>&historicoArquivamento=<?=$historicoArquivamento?>";
            document.frm.submit();
        }
    </script>
    <form name=frm action="arquivaProcesso.php?<?=Sessao::getId()?>&ctrl=4&arquivamento=5" method="post">
<?php

            $sSQL =     "SELECT
                            p.cod_processo || '/' || p.ano_exercicio AS cod_processo,
                            p.cod_processo as cod_processo_original,
                            cl.cod_classificacao || '.' || ass.cod_assunto AS codClassAss,
                            cl.cod_classificacao,
                            cl.nom_classificacao,
                            ass.cod_assunto,
                            ass.nom_assunto,
                            --c.nom_cgm,
                            c2.nom_cgm AS nom_user,
                            co.valor
                            --c.tipo_logradouro||' '||c.logradouro||','||c.numero||' '||c.complemento AS endereco,
                            --c.bairro||' Cep: '||c.cep AS bacep,
                            --mun.nom_municipio,
                            --uff.sigla_uf
                        FROM
                            sw_processo      AS p left outer join administracao.configuracao as co on p.ano_exercicio       = co.exercicio
                                                                                                  and co.parametro          = 'carta_arquivamento_temporario',
                            sw_classificacao AS cl,
                            sw_assunto       AS ass,
                            --sw_cgm           AS c,
                            sw_cgm           AS c2
                            --administracao.configuracao  AS co
                            --sw_municipio     AS mun,
                            --sw_uf            AS uff
                        WHERE
                            p.cod_classificacao   = cl.cod_classificacao            AND
                            ass.cod_classificacao = cl.cod_classificacao            AND
                            p.cod_assunto         = ass.cod_assunto                 AND
                            --p.numcgm              = c.numcgm                        AND
                            --p.ano_exercicio       = co.exercicio                    AND
                            p.cod_usuario         = c2.numcgm                       AND
                            --co.parametro          = 'carta_arquivamento_temporario' AND
                            --c.cod_municipio       = mun.cod_municipio               AND
                            --c.cod_uf              = uff.cod_uf                      AND
                            --mun.cod_uf            = uff.cod_uf                      AND
                            p.cod_processo        = ".$codProcesso."                AND
                            p.ano_exercicio       = '".$anoExercicio."' ";

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $codProcessoOriginal = trim($dbEmp->pegaCampo("cod_processo_original"));
            $codProcesso      = trim($dbEmp->pegaCampo("cod_processo"));
            $codClassificacao = trim($dbEmp->pegaCampo("cod_classificacao"));
            $nomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
            $codAssunto       = trim($dbEmp->pegaCampo("cod_assunto"));
            $nomAssunto       = trim($dbEmp->pegaCampo("nom_assunto"));
            #$nomCgm           = trim($dbEmp->pegaCampo("nom_cgm"));
            $nomUser          = trim($dbEmp->pegaCampo("nom_user"));
            $valorCarta       = trim($dbEmp->pegaCampo("valor"));
            #$endereco         = trim($dbEmp->pegaCampo("endereco"));
            #$bacep            = trim($dbEmp->pegaCampo("bacep"));
            #$cidade           = trim($dbEmp->pegaCampo("nom_municipio"));
            #$uf               = trim($dbEmp->pegaCampo("sigla_uf"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
            $msgEnvio = "complemento=".Sessao::read('texto_complementar').";arquivamento=Temporário";
            include CAM_FW_LEGADO."botoesPdfLegado.class.php";
            $sSQL .=    ";SELECT
                            nom_historico
                        FROM
                            sw_historico_arquivamento
                        WHERE
                            cod_historico = ".$historicoArquivamento;
            $sSQL .= "; SELECT
                            c.valor||',' AS nom_municipio,
                            current_date as hoje
                        FROM
                            administracao.configuracao AS c
                        WHERE
                            c.parametro = 'nom_municipio' and exercicio = '".Sessao::getExercicio()."';";

 print '
        <table width="100">
             <tr>
                 <td class="labelcenter" title="Salvar Relatório">
                 <a href="javascript:SalvarTemporario();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
             </tr>
         </table>
         ';

?>
    <table width="100%">
        <tr>
            <td class="alt_dados" colspan=2>
                Carta de Arquivamento
            </td>
        </tr>

        <tr>
            <td class=label width="30%">
                Código
            </td>
            <td class=field width="70%">
                <?=$codProcesso?>
            </td>
        </tr>
<?php

        // Busca os interessados da base de dados ( ação de alterar processo )
        $sqlQueryInteressdo =
               "SELECT
                        sw_cgm.nom_cgm,
                        sw_processo_interessado.numcgm,
                        *,
                        bairro||' Cep: '||cep AS bacep,
                        sw_municipio.nom_municipio,
                        sw_uf.sigla_uf,
                        tipo_logradouro||' '||logradouro||','||numero||' '||complemento AS endereco
                  FROM  sw_processo_interessado

            INNER JOIN  sw_cgm
                    ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

            INNER JOIN  sw_uf
                    ON  sw_cgm.cod_uf = sw_uf.cod_uf

            INNER JOIN  sw_municipio
                    ON  sw_cgm.cod_municipio = sw_municipio.cod_municipio
                   AND  sw_municipio.cod_uf  = sw_uf.cod_uf

                 WHERE  sw_processo_interessado.cod_processo = ".$codProcessoOriginal."
                   AND  sw_processo_interessado.ano_exercicio = '".$anoExercicio."'";

        $sqlInteressado = new databaseLegado;
        $sqlInteressado->abreBd();
        $sqlInteressado->abreSelecao($sqlQueryInteressdo);

        $i = 1;
        while (!$sqlInteressado->eof()) {
            $numCgm      = trim($sqlInteressado->pegaCampo("numcgm"));
            $nomCgm      = trim($sqlInteressado->pegaCampo("nom_cgm"));
            $endereco    = trim($sqlInteressado->pegaCampo("endereco"));
            $logradouro  = trim($sqlInteressado->pegaCampo("logradouro"));
            $bacep       = trim($sqlInteressado->pegaCampo("bacep"));
            $cidade      = trim($sqlInteressado->pegaCampo("nom_municipio"));
            $uf          = trim($sqlInteressado->pegaCampo("sigla_uf"));
            $numero      = trim($sqlInteressado->pegaCampo("numero"));
            $complemento = trim($sqlInteressado->pegaCampo("complemento"));

?>
            <tr>
                <td class='label' style='text-align:right;'>Destinatário <?=$i;?></td>
                <td class='field'>
                <?=$numCgm;?> - <?=$nomCgm;?><br>
                Endereço: <?=$endereco;?> <?=$logradouro;?>, <?=$numero;?> <?=$complemento;?><br>
                Bairro: <?=$bacep;?><br>
                <?=$cidade;?> / <?=$uf;?>
                </td>
            </tr>
<?php
            $i++;
            $sqlInteressado->vaiProximo();
        }

        $sqlInteressado->limpaSelecao();
        $sqlInteressado->fechaBd();

?>
        <tr>
            <td class=label>
                Classificação/Assunto
            </td>
            <td class=field>
<?php          $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassificacao."-".$codAssunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];
echo $codClassifAssunto;
echo "<br>";
                echo $nomClassificacao;?><br>
                <?=$nomAssunto;?>
            </td>
        </tr>

        <tr>
            <td class=label>
                Solicitado por:
            </td>
            <td class=field>
                <?=$nomUser;?>
            </td>
        </tr>

        <tr>
            <td class=label>
                Arquivamento
            </td>
            <td class=field>
                Temporário
            </td>
        </tr>
        <?php
            $select =   "SELECT
                            nom_historico
                        FROM
                            sw_historico_arquivamento
                        WHERE
                            cod_historico = ".$historicoArquivamento;
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($select);
            $historico = $dbConfig->pegaCampo("nom_historico");
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
        ?>
        <tr>
            <td class=label>
                Motivo do Arquivamento
            </td>
            <td class=field>
                <?=$historico;?>
            </td>
        </tr>

        <?php
            $select =   "SELECT
                            localizacao
                        FROM
                            sw_processo_arquivado
                        WHERE
                            sw_processo_arquivado.cod_historico = ".$historicoArquivamento."
                          AND
                            sw_processo_arquivado.cod_processo = ".$codProcessoOriginal."
                          AND
                            sw_processo_arquivado.ano_exercicio = '".$anoExercicio."'";
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($select);
            $localizacao = $dbConfig->pegaCampo("localizacao");
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
        ?>
        <tr>
            <td class=label>
                Localização Física do Arquivamento
            </td>
            <td class=field>
                <?=$localizacao;?>
            </td>
        </tr>

        <tr>
            <td class=label>
                Texto Complementar
            </td>
            <td class=field>
                <?=nl2br(Sessao::read('texto_complementar'));?>
            </td>
        </tr>

        <tr>
            <td class=label>
                Arquivador
            </td>
            <td class=field>
                <?=Sessao::read('nomCgm');?>
            </td>
        </tr>

    </table>
<?php

break;
//#########################
case 9:
//#########################

?>
    <script type="text/javascript">
        function SalvarDefinitivo()
        {
            document.frm.action = "arquivaProcessoDefinitivo.php?<?=Sessao::getId()?>&ctrl=4&arquivamento=9&codProcesso=<?=$codProcesso?>&anoExercicio=<?=$anoExercicio?>&historicoArquivamento=<?=$historicoArquivamento?>";
            document.frm.submit();
        }
    </script>
    <form name=frm action="arquivaProcesso.php?<?=Sessao::getId()?>&ctrl=4&arquivamento=9" method="post">
<?php

            $sSQL =     "SELECT
                            p.cod_processo || '/' || p.ano_exercicio AS cod_processo,
                            p.cod_processo as cod_processo_original ,
                            cl.cod_classificacao || '.' || ass.cod_assunto AS codClassAss,
                            cl.cod_classificacao,
                            cl.nom_classificacao,
                            ass.cod_assunto,
                            ass.nom_assunto,
                            --c.nom_cgm,
                            u.username AS nom_user,
                            co.valor
                            --c.tipo_logradouro||' '||c.logradouro||','||c.numero||' '||c.complemento AS endereco,
                            --c.bairro||' Cep: '||c.cep AS bacep,
                            --mun.nom_municipio,
                            --uff.sigla_uf
                        FROM
                            sw_processo      AS p left outer join administracao.configuracao as co on p.ano_exercicio       = co.exercicio
                                                                                                  and co.parametro          = 'carta_arquivamento_definitivo',
                            sw_classificacao AS cl,
                            sw_assunto       AS ass,
                            --sw_cgm           AS c,
                            administracao.usuario       AS u
                            --administracao.configuracao  AS co,
                            --sw_municipio     AS mun,
                            --sw_uf            AS uff
                        WHERE
                            p.cod_classificacao   = cl.cod_classificacao            AND
                            ass.cod_classificacao = cl.cod_classificacao            AND
                            p.cod_assunto         = ass.cod_assunto                 AND
                            --p.ano_exercicio       = co.exercicio                    AND
                            --p.numcgm              = c.numcgm                        AND
                            p.cod_usuario         = u.numcgm                        AND
                            --co.parametro          = 'carta_arquivamento_definitivo' AND
                            --c.cod_municipio       = mun.cod_municipio               AND
                            --c.cod_uf              = uff.cod_uf                      AND
                            --mun.cod_uf            = uff.cod_uf                      AND
                            p.cod_processo        = ".$codProcesso."                AND
                            p.ano_exercicio       = '".$anoExercicio."' ";

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $codProcessoOriginal = trim($dbEmp->pegaCampo("cod_processo_original"));
            $codProcesso      = trim($dbEmp->pegaCampo("cod_processo"));
            $codClassificacao = trim($dbEmp->pegaCampo("cod_classificacao"));
            $nomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
            $codAssunto       = trim($dbEmp->pegaCampo("cod_assunto"));
            $nomAssunto       = trim($dbEmp->pegaCampo("nom_assunto"));
            #$nomCgm           = trim($dbEmp->pegaCampo("nom_cgm"));
            $nomUser          = trim($dbEmp->pegaCampo("nom_user"));
            $valorCarta       = trim($dbEmp->pegaCampo("valor"));
            #$endereco         = trim($dbEmp->pegaCampo("endereco"));
            #$bacep            = trim($dbEmp->pegaCampo("bacep"));
            #$cidade           = trim($dbEmp->pegaCampo("nom_municipio"));
            #$uf               = trim($dbEmp->pegaCampo("sigla_uf"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
            $msgEnvio = "complemento=".Sessao::read('texto_complementar').";arquivamento=Definitivo";
            include CAM_FRAMEWORK."legado/botoesPdfLegado.class.php";
            $sSQL .=    ";SELECT
                            nom_historico
                        FROM
                            sw_historico_arquivamento
                        WHERE
                            cod_historico = ".$historicoArquivamento;
            $sSQL .= "; SELECT
                            c.valor||',' AS nom_municipio,
                            current_date AS hoje
                        FROM
                            administracao.configuracao AS c
                        WHERE
                            c.parametro = 'nom_municipio' AND c.exercicio = '".Sessao::getExercicio()."'";

 print '
        <table width="100">
             <tr>
                 <td class="labelcenter" title="Salvar Relatório">
                 <a href="javascript:SalvarDefinitivo();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
             </tr>
         </table>
         ';

?>
    <table width="100%">

        <tr>
            <td class="alt_dados" colspan=2>
                Carta de Arquivamento
            </td>
        </tr>

        <tr>
            <td class=label width="30%">
                Código
            </td>
            <td class=field width="70%">
                <?=$codProcesso?>
            </td>
        </tr>
<?php

        // Busca os interessados da base de dados ( ação de alterar processo )
        $sqlQueryInteressdo =
               "SELECT
                        sw_cgm.nom_cgm,
                        sw_processo_interessado.numcgm,
                        *,
                        bairro||' Cep: '||cep AS bacep,
                        sw_municipio.nom_municipio,
                        sw_uf.sigla_uf,
                        tipo_logradouro||' '||logradouro||','||numero||' '||complemento AS endereco
                  FROM  sw_processo_interessado

            INNER JOIN  sw_cgm
                    ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

            INNER JOIN  sw_uf
                    ON  sw_cgm.cod_uf = sw_uf.cod_uf

            INNER JOIN  sw_municipio
                    ON  sw_cgm.cod_municipio = sw_municipio.cod_municipio
                   AND  sw_municipio.cod_uf  = sw_uf.cod_uf

                 WHERE  sw_processo_interessado.cod_processo = ".$codProcessoOriginal."
                   AND  sw_processo_interessado.ano_exercicio = '".$anoExercicio."'";

        $sqlInteressado = new databaseLegado;
        $sqlInteressado->abreBd();
        $sqlInteressado->abreSelecao($sqlQueryInteressdo);

        $i = 1;
        while (!$sqlInteressado->eof()) {
            $numCgm      = trim($sqlInteressado->pegaCampo("numcgm"));
            $nomCgm      = trim($sqlInteressado->pegaCampo("nom_cgm"));
            $endereco    = trim($sqlInteressado->pegaCampo("endereco"));
            $logradouro  = trim($sqlInteressado->pegaCampo("logradouro"));
            $bacep       = trim($sqlInteressado->pegaCampo("bacep"));
            $cidade      = trim($sqlInteressado->pegaCampo("nom_municipio"));
            $uf          = trim($sqlInteressado->pegaCampo("sigla_uf"));
            $numero      = trim($sqlInteressado->pegaCampo("numero"));
            $complemento = trim($sqlInteressado->pegaCampo("complemento"));

?>
            <tr>
                <td class='label' style='text-align:right;'>Destinatário <?=$i;?></td>
                <td class='field'>
                <?=$numCgm;?> - <?=$nomCgm;?><br>
                Endereço: <?=$endereco;?> <?=$logradouro;?>, <?=$numero;?> <?=$complemento;?><br>
                Bairro: <?=$bacep;?><br>
                <?=$cidade;?> / <?=$uf;?>
                </td>
            </tr>
<?php
            $i++;
            $sqlInteressado->vaiProximo();
        }

        $sqlInteressado->limpaSelecao();
        $sqlInteressado->fechaBd();

?>
        <tr>
            <td class=label>
                Classificação/Assunto
            </td>
            <td class=field>
<?php          $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassificacao."-".$codAssunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];
echo $codClassifAssunto;
echo "<br>";
                echo
                $nomClassificacao;?><br>
                <?=$nomAssunto;?>
            </td>
        </tr>

        <tr>
            <td class=label>
                Incluido por
            </td>
            <td class=field>
                <?=$nomUser;?>
            </td>
        </tr>

        <tr>
            <td class=label>
                Arquivamento
            </td>
            <td class=field>
                Definitivo
            </td>
        </tr>
        <?php
            $select =   "SELECT
                            nom_historico
                        FROM
                            sw_historico_arquivamento
                        WHERE
                            cod_historico = ".$historicoArquivamento.";";
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($select);
            $historico = $dbConfig->pegaCampo("nom_historico");
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
        ?>
        <tr>
            <td class=label>
                Motivo do Arquivamento
            </td>
            <td class=field>
                <?=$historico;?>
            </td>
        </tr>
        <?php
            $select =   "SELECT
                            localizacao
                        FROM
                            sw_processo_arquivado
                        WHERE
                            sw_processo_arquivado.cod_historico = ".$historicoArquivamento."
                          AND
                            sw_processo_arquivado.cod_processo = ".$codProcessoOriginal."
                          AND
                            sw_processo_arquivado.ano_exercicio = '".$anoExercicio."'";
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($select);
            $localizacao = $dbConfig->pegaCampo("localizacao");
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
        ?>
        <tr>
            <td class=label>
                Localização Física do Arquivamento
            </td>
            <td class=field>
                <?=$localizacao;?>
            </td>
        </tr>

        <tr>
            <td class=label>
                Texto Complementar
            </td>
            <td class=field>
                <?=nl2br(Sessao::read('texto_complementar'));?>
            </td>
        </tr>
        <tr>
            <td class=label>
                Arquivador
            </td>
            <td class=field>
                <?=Sessao::read('nomCgm');?>
            </td>
        </tr>

    </table>
<?php

break;
//#########################
}
//*************************
//*************************

break;
case 100:
    include(CAM_FRAMEWORK."legado/filtrosCASELegado.inc.php");
break;
}

include '../../../framework/include/rodape.inc.php';
?>
