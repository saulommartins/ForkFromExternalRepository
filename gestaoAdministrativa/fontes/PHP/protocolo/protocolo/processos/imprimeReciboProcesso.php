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

    $Id: imprimeReciboProcesso.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_FW_LEGADO."paginacaoLegada.class.php";
include_once CAM_FW_LEGADO."botoesPdfLegado.class.php";
include_once CAM_FW_LEGADO."mascarasLegado.lib.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

$codMasSetor       = $_REQUEST['codMasSetor'];
$codProcesso       = $_REQUEST['codProcesso'];
$anoExercicio      = $_REQUEST['anoExercicio'];
$anoExercicioSetor = $_REQUEST['anoExercicioSetor'];
$stParametros      = $_REQUEST['stParametros'];
$pagina            = $_REQUEST['pagina'];
$registroFunc      = $_REQUEST['registroFunc'];

$arInteressados = Sessao::getRequestProtocolo();
$interessado    = $arInteressados['interessados'];
$permitido      = $arInteressados['permitidos'];

?>

<script type="text/javascript">

    function ImprimirEtiqueta()
    {
        document.frm.action = "imprimirEtiqueta.php?<?=Sessao::getId()?>&codMasSetor=<?=$codMasSetor?>&anoExercicioSetor<?=$anoExercicioSetor?>&stParametros=sessao&codProcesso=<?=$_REQUEST['codProcesso']?>&anoExercicio=<?=$_REQUEST['anoExercicio']?>";
        document.frm.submit();
    }

     function SalvarRecibo()
     {
        document.frm.action = "reciboProcesso.php?<?=Sessao::getId()?>&codMasSetor=<?=$codMasSetor?>&anoExercicioSetor<?=$anoExercicioSetor?>&stParametros=sessao&codProcesso=<?=$_REQUEST['codProcesso']?>&anoExercicio=<?=$_REQUEST['anoExercicio']?>";
        document.frm.submit();
    }

    function ImprimeDespachos()
    {
        document.frm.action = "imprimeRelatorioDespachos.php?<?=Sessao::getId()?>&codMasSetor=<?=$codMasSetor?>&anoExercicioSetor<?=$anoExercicioSetor?>&stParametros=sessao&codProcesso=<?=$_REQUEST['codProcesso']?>&anoExercicio=<?=$_REQUEST['anoExercicio']?>";
        document.frm.submit();
    }

</script>

    <form name=frm action="imprimeReciboProcesso.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&verificador=false" method="post"></form>

<?php

if ($_GET["stParametros"] == "sessao") {
    $arParametros = Sessao::read('arParametros');
    foreach ($arParametros AS $stNomeVar => $stValor) {
        $$stNomeVar = $stValor;
    }
}

$iCodProcesso  = (int) $iCodProcesso;
$sAnoExercicio = (string) $sAnoExercicio;

if ($_REQUEST['anoExercicioSetor']) {
    $anoExercicio = $_REQUEST['anoExercicioSetor'];
}

if ( empty($nomCgm) and $numCgm ) {
    $nomCgm = SistemaLegado::pegaDado("nom_cgm","sw_cgm","where numcgm=".$numCgm);
}

if ($iCodProcesso==0 or strlen($sAnoExercicio)==0) {
    exit('
        <script type="text/javascript">
            alertaAviso("Código do Processo Inexistente ou Ano exercício Inválido","unica","erro","'.Sessao::getId().'");
            mudaTelaPrincipal("listaSituacaoProcesso.php?'.Sessao::getId().'");
        </script>');
}

$sSQL = "";
$sSQL .= "
        SELECT
                p.cod_processo || '/' || p.ano_exercicio as cod_ano_processo,
                c.nom_classificacao, a.nom_assunto, p.timestamp
          FROM  sw_processo p,
                sw_classificacao c,
                sw_assunto a
         WHERE  p.cod_processo = $iCodProcesso
           AND  p.ano_exercicio = '".$sAnoExercicio."'
           AND  c.cod_classificacao = p.cod_classificacao
           AND  a.cod_classificacao = p.cod_classificacao
           AND  a.cod_assunto = p.cod_assunto;";

$sSQL .= "
            SELECT
                p.cod_processo,
                p.numcgm,
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
                cj.numcgm       = p.numcgm        AND
                c.numcgm        = p.numcgm        AND
                m.cod_municipio = c.cod_municipio AND
                m.cod_uf        = c.cod_uf        AND
                p.cod_processo  = $iCodProcesso   AND
                p.ano_exercicio = '".$sAnoExercicio."'
            UNION
                SELECT
                    p.cod_processo,
                    p.numcgm,
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
                    cf.numcgm       = p.numcgm        AND
                    c.numcgm        = p.numcgm        AND
                    m.cod_municipio = c.cod_municipio AND
                    m.cod_uf        = c.cod_uf        AND
                    p.cod_processo  = $iCodProcesso   AND
                    p.ano_exercicio = '".$sAnoExercicio."'
            UNION
                SELECT
                    p.cod_processo,
                    p.numcgm,
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
                    c.numcgm        = p.numcgm         AND
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
$sqlCodSituacao = "SELECT cod_situacao FROM sw_processo WHERE cod_processo = ".$arParametros['iCodProcesso']." AND ano_exercicio = '".$arParametros['sAnoExercicio']."' ";

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

// INFORMA O SETOR INICIAL E O SETOR DE DESTINO DO PROCESSO
$sSQL .= "
        SELECT    p.cod_processo
                , c.nom_classificacao
                , a.nom_assunto
                , p.observacoes
                , p.timestamp
                , s.nom_situacao
                , '".$arCodSetor[1]." - ".$nomSetor." ".$stSituacao."' as setorFinal
                , '".$arCodSetorInicial[1]." - ".$nomSetorInicial."' as setorInicial
          FROM  sw_processo p
                , sw_situacao_processo s
                , sw_classificacao c
                , sw_assunto a
         WHERE  p.cod_processo  = $iCodProcesso
           AND  p.ano_exercicio = '".$sAnoExercicio."'
           AND  c.cod_classificacao = p.cod_classificacao
           AND  a.cod_classificacao = p.cod_classificacao
           AND  a.cod_assunto = p.cod_assunto
           AND  s.cod_situacao   = p.cod_situacao;";

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
    SELECT
              dp.cod_processo
            , d.nom_documento
      FROM  sw_documento d
            , sw_documento_processo dp
     WHERE  dp.cod_processo = $iCodProcesso
       AND  dp.exercicio    = '".$sAnoExercicio."'
       AND  d.cod_documento = dp.cod_documento;";

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
//echo $sSQL."<br>";
$sSubTitulo    = "Processo número ".$iCodProcesso."/".$sAnoExercicio;
$caminhoRecibo = CAM_PROTOCOLO.pegaConfiguracao("caminho_recibo_processo", 5);
$sXML          = $caminhoRecibo;
$stMensagemRecibo = pegaConfiguracao("mensagem_recibo_processo", 5);
$botoesPDF  = new botoesPdfLegado;
if (file_exists($caminhoRecibo)) {
//	$botoesPDF->imprimeBotoes($sXML,$sSQL,'',$sSubTitulo);
    if(isset($_REQUEST['stAcao'])&&$_REQUEST['stAcao']=='Incluir'){
        print '
            <table width="300">
                <tr>
                    <td class="labelcenter" title="Salvar Relatório">
                    <a href="javascript:SalvarRecibo();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                    <td class="labelcenter" title="Imprimir Etiqueta">
                    <a href="javascript:ImprimirEtiqueta();"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a>
                </tr>
            </table>
        '; 
    }
    else{
        print '
            <table width="300">
                <tr>
                    <td class="labelcenter" title="Salvar Relatório">
                    <a href="javascript:SalvarRecibo();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                    <td class="labelcenter" title="Imprimir Etiqueta">
                    <a href="javascript:ImprimirEtiqueta();"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a>
                    <td class="labelcenter" title="Imprimir Despachos do Processo">
                    <a href="javascript:ImprimeDespachos();"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a>
                </tr>
            </table>
        ';
    }

} else {
    echo "<script type='text/javascript'>
            alertaAviso('Erro ao Carregar o arquivo XML, verifique os parâmetros','unica','erro', '".Sessao::getId()."');
        </script>";
}
?>
<table width="100%">
    <tr>
        <td class=alt_dados colspan="3">
            Dados do(s) Interessado(s)
        </td>
    </tr>
<?php
if ($vinculo == "imobiliaria") {
?>
    <tr>
        <td class=label width="30%">
            Incrição cadastral
        </td>
        <td class=field width="70%" colspan="2">
            <?=$numMatricula?>
        </td>
    </tr>
<?php
}

if ($vinculo == "inscricao") {
?>
    <tr>
        <td class=label width="30%">
            Inscrição cadastral
        </td>
        <td class=field width="70%" colspan="2">
            <?=$numInscricao?>
        </td>
    </tr>

<?php
}

if ($vinculo == "funcionario") {
?>
    <tr>
        <td class=label width="30%">
            Registro do(s) Funcionário(s)
        </td>
        <td class=field width="70%" colspan="2">
            <?=$registroFunc?>
        </td>
    </tr>

<?php
}
?>

<?php
    $i = 1;
    if (count($interessado) > 0) {
        foreach ($interessado as $chave => $valor) {
?>
    <tr>
        <td class=label width="35%" style="text-align:right;">
            <strong>Interessado <?=$i;?></strong>
        </td>
        <td class=field width="65%">
            <?=$valor['numCgm']?> - <?=$valor['nomCgm']?>
        </td>
    </tr>
<?php
          $i++;
    }
    } else {
        // Busca os interessados da base de dados ( ação de alterar processo )
        $sqlQueryInteressado =
               "SELECT  sw_cgm.nom_cgm, sw_processo_interessado.numcgm
                  FROM  sw_processo_interessado
            INNER JOIN  sw_cgm
                    ON  sw_cgm.numcgm = sw_processo_interessado.numcgm
                 WHERE  sw_processo_interessado.cod_processo = ".$iCodProcesso."
                   AND  sw_processo_interessado.ano_exercicio = '".$sAnoExercicio."' ";

        $sqlInteressado = new databaseLegado;
        $sqlInteressado->abreBd();
        $sqlInteressado->abreSelecao($sqlQueryInteressado);

        while (!$sqlInteressado->eof()) {
            $numCgm = $sqlInteressado->pegaCampo("numcgm");
            $nomCgm = $sqlInteressado->pegaCampo("nom_cgm");

?>
    <tr>
        <td class=label width="35%" style="text-align:right;">
        <strong>Interessado <?=$i;?></strong>
        </td>
        <td class=field width="65%">
          <?=$numCgm?> - <?=$nomCgm?>
        </td>
    </tr>
<?php

        $i++;
        $sqlInteressado->vaiProximo();
    }

        $sqlInteressado->limpaSelecao();
        $sqlInteressado->fechaBd();

    }
?>
    <tr>
        <td class=alt_dados colspan="3">
            Dados de processo
        </td>
    </tr>
    <tr>
        <td class=label width="30%">
            Código
        </td>
        <td class=field width="70%" colspan="2">
            <?php
                $mascaraProcesso = pegaConfiguracao("mascara_processo", 5);
                $codProcessoC    = $iCodProcesso.$sAnoExercicio;
                $numCasas        = strlen($mascaraProcesso) - 1;
                $iCodProcessoS   = str_pad($codProcessoC, $numCasas, "0" ,STR_PAD_LEFT);
                $iCodProcessoS   = geraMascaraDinamica($mascaraProcesso, $iCodProcessoS);
                echo $iCodProcessoS;
            ?>
        </td>
    </tr>
    <tr>
        <td class=label width="30%">
            Classificação/Assunto
        </td>
        <td class=field width="70%" colspan="2">
<?php
            $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassif."-".$codAssunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];
            echo $codClassifAssunto."<br>";
                $select = 	"SELECT
                                nom_classificacao,
                                nom_assunto
                            FROM
                                sw_classificacao AS C,
                                sw_assunto       AS A
                            WHERE
                                C.cod_classificacao = ".$codClassif." AND
                                A.cod_assunto       = ".$codAssunto." AND
                                A.cod_classificacao = C.cod_classificacao";
                //echo $codClassificacao;
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $dbConfig->abreSelecao($select);

                $nomClassificacao = $dbConfig->pegaCampo("nom_classificacao");
                $nomAssunto       = $dbConfig->pegaCampo("nom_assunto");

                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();
            ?>
            <?=$nomClassificacao?><br>
            <?=$nomAssunto?>
        </td>
    </tr>
    
    <?php
    $centroCusto = pegaConfiguracao("centro_custo", 5);
    
    if($centroCusto=='true'){
        $codCentroCusto = '';
        $nomCentroCusto = '';
        $stCentroCusto = '';
        
        $sSQL = "SELECT sw_processo.*
                      , centro_custo.descricao as descricao_centro
                   FROM sw_processo
             INNER JOIN almoxarifado.centro_custo
                     ON centro_custo.cod_centro=sw_processo.cod_centro
                  WHERE ano_exercicio = '".$sAnoExercicio."'
                    AND cod_processo = ".$iCodProcesso;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBD();
        $dbConfig->abreSelecao($sSQL);
        $dbConfig->vaiPrimeiro();
        while (!$dbConfig->eof()) {
            $codCentroCusto  = $dbConfig->pegaCampo("cod_centro");
            $nomCentroCusto  = trim($dbConfig->pegaCampo("descricao_centro"));
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBD();
        
        if($codCentroCusto!=''&&$nomCentroCusto!='')
           $stCentroCusto = $codCentroCusto." - ".$nomCentroCusto;
           
        echo "
        <tr>
            <td class=label width='30%'>
                Centro de Custo
            </td>
            <td class=field width='70%' colspan='2'>
                ".$stCentroCusto."
            </td>
        </tr>
        ";
    }
    ?>
    
    <tr>
        <td class=label width="30%">
            Observações
        </td>
        <td class=field width="70%" colspan="2">
            <?php
            $observacoes = stripslashes($observacoes);
            $observacoes = str_replace("/*-/*-", "<br>", $observacoes);
            if ($observacoes == "") {
                $observacoes = "&nbsp;";
            }
            ?>
            <?=$observacoes?>
        </td>
    </tr>

    <tr>
        <td class=label width="30%">Confidencial</td>
        <td class=field width="70%" colspan="2"><?=(count($permitido) > 0) ? 'Sim' : 'Não';?>
        </td>
    </tr>

    <?php
        if (count($permitido) > 0) {
            foreach ($permitido as $key => $valor) {
    ?>
            <tr>
                <td class=label width="30%">CGM com Acesso ao Processo </td>
                <td class=field width="70%" colspan="2"><?=$valor['numCgmAcesso']?> - <?=$valor['nomCgmAcesso']?>
                </td>
            </tr>
    <?php
            }
        }
    ?>

    <tr>
        <td class=alt_dados colspan="3">
            Encaminhamento de processo
        </td>
    </tr>
    </table>
    <?php

        $obFormulario = new Formulario;
        $obFormulario->addForm(null);
        $obFormulario->setLarguraRotulo(35);

        $obIMontaOrganograma = new IMontaOrganograma(true);
        $obIMontaOrganograma->setCodOrgao($codOrgao);
        $obIMontaOrganograma->setComponenteSomenteLeitura(true);
        $obIMontaOrganograma->geraFormulario($obFormulario);

        $obFormulario->montaHTML();
        echo $obFormulario->getHTML();

    ?>
    <table width='100%'>
        <tr>
            <td class=label width="35%">
                Mensagem
            </td>
            <td class=field width="70%" colspan="2">
            <?=$stMensagemRecibo?>
            </td>
        </tr>
    </table>
<?php

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
