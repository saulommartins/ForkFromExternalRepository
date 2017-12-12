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
    * Relatório Bem Entidade
    * Data de Criação   : 22/11/200a

    * @author Desenvolvedor  Vandré Miguel Ramos

    * @ignore

    $Revision: 28967 $
    $Name$
    $Autor: $
    $Date: 2008-04-02 17:38:21 -0300 (Qua, 02 Abr 2008) $

    * Casos de uso: uc-03.01.20
*/

/*
$Log$
Revision 1.29  2006/07/21 11:36:18  fernando
Inclusão do  Ajuda.

Revision 1.28  2006/07/13 20:47:34  fernando
Alteração de hints

Revision 1.27  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.26  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.20");
if (!(isset($ctrl))) {
    $ctrl = 0;
}

if (isset($pagina)) {
    $ctrl = 1;
}

switch ($ctrl_frm) {
    // preenche Natureza, Grupo e Especie
    case 2:
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosNGE.inc.php';
        exit();
    break;
    case 3:

            $sSQL = "SELECT
                        C.numcgm,
                        C.nom_cgm
                    FROM
                        sw_cgm as C,
                        orcamento.entidade AS OE
                    WHERE
                        C.numcgm = OE.numcgm
                    GROUP BY
                        C.numcgm
                        ,C.nom_cgm
                    ORDER BY
                        C.nom_cgm ";

                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                            // monta combo com Entidade
                     $i = 1;
                     $js .= 'f.codEntidade.options.length = 1;';
                     while (!$dbEmp->eof()) {
                        $js .= 'f.codEntidade.options['.$i.'] = new Option(\''.$dbEmp->pegaCampo('nom_cgm').'\',\'';
                        $js .= $dbEmp->pegaCampo('numcgm').'\');';
                        $dbEmp->vaiProximo();
                        $i++;
                     }

               executaFrameOculto($js);
               exit();

    break;
}

switch ($ctrl) {
    case 0:
?>
        <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var Vnatureza;
            var Vgrupo;
            var Vespecie;
            var Ventidade;
            var Vdataini;
            var Vdatafin;
            var campoaux;

            Vnatureza  = document.frm.codEntidade.value;
            Vgrupo     = document.frm.codGrupo.value;
            Vespecie   = document.frm.codEspecie.value;
            Ventidade  = document.frm.codEntidade.value;
            Vdataini   = document.frm.dataInicial.value;

            Vdatafin  = document.frm.dataFinal.value;
            if (Ventidade == 'xxx') {
              mensagem +="É necessário selecionar uma entidade!";
              erro = true;
            }
            if ((Vdataini == '')  && (erro == false)) {
              mensagem +="É necessário informar a data inicial!";
              erro = true;
            }
            if ((Vdatafin == '')  && (erro == false)) {
              mensagem +="É necessário informar a data final!";
              erro = true;
            }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
        }
        function Salvar()
        {
            if (Valida()) {
                    document.frm.action = "relatorioBemEntidade.php?<?=Sessao::getId()?>&ctrl=1";
                    document.frm.target = 'telaPrincipal';
                    document.frm.ctrl.value = '1';
                    document.frm.ctrl_frm.value = '1';
                    document.frm.submit();
            }
        }

        // preenche os combos de Natureza, Grupo e Especie
        function preencheNGE(variavel, valor)
        {
            document.frm.target = "oculto";
            document.frm.ctrl.value = '1';
            document.frm.ctrl_frm.value = "2";
            document.frm.variavel.value = variavel;
            document.frm.valor2.value = valor;
            document.frm.submit();
        }

        function preencheTodosNGE(valor)
        {
            document.frm.action ="";
            document.frm.target = "oculto";
            document.frm.ctrl.value = '2';
            document.frm.ctrl_frm.value = "2";
            document.frm.variavel.value = 'NatGrpEsp';
            document.frm.valor2.value = valor;
            document.frm.submit();
        }

        function busca_entidades(cod)
        {
                document.frm.ctrl.value = "<?=$ctrl;?>";

                var f = document.frm;
                f.target = 'oculto';
                f.ctrl_frm.value = cod;
                f.submit();
            }

        // desabilita botao 'OK' se o valor informado no input text nao existir e vice-versa
        // submete o formulario para preencher os campos dependentes ao combo selecionado
        function verificaCombo(campo_a, campo_b)
        {
            var aux;
            aux = preencheCampo(campo_a, campo_b);
            if (aux == false) {
                    document.frm.ok.disabled = true;
            } else {
                    document.frm.ok.disabled = false;
                    }
            preencheNGE(campo_b.name, campo_b.value)
        }

        function Limpar()
        {
            var grupo = document.getElementById('inCodGrupo');

            var tam = grupo.options.length;
            while (tam >= 1) {
                    grupo.options[tam] = null;
                    tam = tam - 1 ;
            }

            var especie = document.getElementById('inCodEspecie');

            var tam = especie.options.length;
            while (tam >= 1) {
                    especie.options[tam] = null;
                    tam = tam - 1 ;
            }
            document.frm.reset();
    }

        </script>

<?PHP

        include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php" );
        include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php" );
        include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php" );

        $obTPatrimonioNatureza = new TPatrimonioNatureza();
        $obTPatrimonioNatureza->recuperaMaxNatureza( $rsMaxCodNatureza );
        $tamanhoMaxNat = strlen($rsMaxCodNatureza->arElementos[0]['max']) ;

        $obTPatrimonioGrupo = new TPatrimonioGrupo();
        $obTPatrimonioGrupo->recuperaMaxGrupoCombo( $inMaxCodGrupo );
        $tamanhoMaxGr = strlen($inMaxCodGrupo->arElementos[0]['max'])  + 1;

        $obTPatrimonioEspecie = new TPatrimonioEspecie();
        $obTPatrimonioEspecie->recuperaMaxEspecie( $rsMaxCodEspecie );
        $tamanhoMaxEs = strlen($rsMaxCodEspecie->arElementos[0]['max']) + 1;

        $valorMascara = "'".str_pad('9',$tamanhoMaxNat-1,'9').'.'.str_pad('9',$tamanhoMaxGr-1,'9').'.'.str_pad('9',$tamanhoMaxEs-1,'9')."'";
        $tamanhoCampo = $tamanhoMaxNat + $tamanhoMaxGr + $tamanhoMaxEs;

?>

        <form name="frm" action="relatorioBemEntidade.php?<?=Sessao::getId()?>" method="POST">
            <input type="hidden" name="ctrl" value=''>
            <input type="hidden" name="ctrl_frm" value=''>
        <!--os hiddens abaixo sao usados para a manipulacao dos filtros CALO e NGE.-->
            <input type="hidden" name="variavel" value=''>
            <input type="hidden" name="valor2" value=''>

        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="2">Insira os Dados para Procura</td>
        </tr>
        <tr>
            <td class="label" >
                Classificação
            </td>
            <td class="field" >
                <input id="stCodClassificacao" type="text" align="left" size="<?PHP echo $valorMascara; ?>" maxlength="<?PHP echo $tamanhoCampo; ?>" onblur="JavaScript:preencheTodosNGE(this.value );" onkeyup="JavaScript:mascaraDinamico(<?PHP echo $valorMascara; ?>, this, event);" tabindex="1" name="stCodClassificacao"/>
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Selecione a natureza do bem.">Natureza</td>
            <td class='field' width="80%">
                <select name='codNatureza' onChange="javascript: preencheNGE('codNatureza', this.value);" style="width:320px">
                    <option value="xxx" SELECTED>Selecione</option>
<?php
                    // busca Naturezas cadastradas
                    $sSQL = "SELECT
                                cod_natureza, nom_natureza
                                FROM
                                patrimonio.natureza
                                ORDER
                                by nom_natureza";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                            // monta combo com Naturezas
                    $comboCodNatureza = "";
                    while (!$dbEmp->eof()) {
                            $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                            $nomNaturezaf  = trim($dbEmp->pegaCampo("nom_natureza"));
                            $chave = $codNaturezaf;
                            $dbEmp->vaiProximo();
                            $comboCodNatureza .= "<option value='".$chave."'";
                            if (isset($codNatureza)) {
                                if ($chave == $codNatureza) {
                                        $comboCodNatureza .= " SELECTED";
                                        $nomNatureza = $nomNaturezaf;
                                        }
                                }
                            $comboCodNatureza .= ">".$codNaturezaf." - ".$nomNaturezaf."</option>\n";
                            }
                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboCodNatureza;
?>
                </select>
                <input type="hidden" name="nomNatureza" value="">
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Selecione o grupo do bem.">Grupo</td>
            <td class="field">
                <select id='inCodGrupo' name="codGrupo" onChange="javascript: preencheNGE('codGrupo', this.value);" style="width:320px">
                        <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomGrupo" value="">
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Selecione a espécie do bem.">Espécie</td>
            <td class="field">
                <select id='inCodEspecie'  name="codEspecie" onChange="javascript: preencheNGE('codEspecie', this.value);" style="width:320px" >
                        <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomEspecie" value="">
            </td>
        </tr>
        <tr>
            <td class="label" width="20%" title="Selecione a entidade para o filtro.">*Entidade</td>
            <td class='field' width="80%">
                <select name='codEntidade' style="width:320px">
                    <option value='xxx' SELECTED>Selecione</option>
<?php
                    // busca Naturezas cadastradas

            $sSQL = "SELECT
                        C.numcgm,
                        C.nom_cgm
                    FROM
                        sw_cgm as C,
                        orcamento.entidade AS OE
                    WHERE
                        C.numcgm = OE.numcgm
                    GROUP BY
                        C.numcgm
                        ,C.nom_cgm
                    ORDER BY
                        C.nom_cgm ";

                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                            // monta combo com Entidade
                    $comboCodNatureza = "";
                    while (!$dbEmp->eof()) {
                            $codEntidadef  = trim($dbEmp->pegaCampo("numcgm"));
                            $nomEntidadef  = trim($dbEmp->pegaCampo("nom_cgm"));
                            $chave = $codEntidadef;
                            $dbEmp->vaiProximo();
                            $comboCodEntidade .= "<option value='".$chave."'";
                            if (isset($codEntidade)) {
                                if ($chave == $codEntidade) {
                                        $comboCodEntidade .= " SELECTED";
                                        $nomEntidade = $nomEntidadef;
                                        }
                                }
                            $comboCodEntidade .= ">".$nomEntidadef."</option>\n";
                            }
                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboCodEntidade;
?>
                </select>
                <input type="hidden" name="nomEntidade" value="">
            </td>
        </tr>
<?php
   geraCampoData2("*Data Inicial", "dataInicial", "01/03/2003", false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value = '';};\"","Selecione a data inicial","Buscar data inicial" );
   geraCampoData2("*Data Final", "dataFinal", hoje(), false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value = '';};\"","Informe a data final","Buscar data final" );
?>
    <tr>
            <td class="label" title = 'Selecione a ordenação do relatório.'>Ordenação</td>
            <td class="field">
                <select name="ordenacao">
                    <option value="codigo">Código</option>
            <option value="classificacao">Classificação</option>
                    <option value="descricao">Descrição</option>
                </select>
            </td>
        </tr>
        </tr>

       <tr>
            <td class="field" colspan="2"><?php geraBotaoOk2(1,1); ?></td>
        </tr>
      </table>
      </form>
<?php
    break;
    case 1:
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';

       $variaveis = $HTTP_POST_VARS;

       $codNatureza = $variaveis['codNatureza'];
       $codGrupo    = $variaveis['codGrupo'];
       $codEspecie  = $variaveis['codEspecie'];
       $codEntidade = $variaveis['codEntidade'];

       //monta condição Where

       if ($codNatureza != 'xxx') {
          $stWhere .= " AND b.cod_natureza = ".$codNatureza;
       } else { $stWhere .= '';}

       if ($codGrupo != 'xxx') {
          $stWhere .= " AND b.cod_grupo = " .$codGrupo;
       } else { $stWhere .= '';}

       if ($codEspecie != 'xxx') {
          $stWhere .= " AND b.cod_especie =" .$codEspecie;
       } else { $stWhere .= '';}

       //monta Ordenação

       if ($ordenacao == 'classificacao') {
              $stOrder = "  ORDER BY empenho";
           } elseif ($ordenacao == 'codigo') {
              $stOrder = "  ORDER BY codigo";
           } elseif ($ordenacao == 'descricao') {
              $stOrder = "  ORDER BY UPPER(b.descricao)";
           }

        $ArrData     = explode("/", $dataInicial);
        $dataInicial = $ArrData[2] . "-" . $ArrData[1] . "-" . $ArrData[0];
        $ArrData     = explode("/", $dataFinal);
        $dataFinal   = $ArrData[2] . "-" . $ArrData[1] . "-" . $ArrData[0];

$sSQLPDF  ="  SELECT    \n";
$sSQLPDF .="       b.cod_natureza|| '.' ||b.cod_grupo|| '.' ||b.cod_especie AS classificacao  \n";
$sSQLPDF .="      ,b.cod_bem                                                AS codigo  \n";
$sSQLPDF .="      ,b.descricao                                              AS descricao  \n";
$sSQLPDF .="      ,bc.exercicio                                                            \n";
$sSQLPDF .="      ,bc.cod_empenho||'/'||bc.exercicio                        AS empenho  \n";
$sSQLPDF .="      ,lo.nom_local                                                         AS local  \n";
$sSQLPDF .="  FROM  \n";
$sSQLPDF .="      patrimonio.bem as b  \n";
$sSQLPDF .="      LEFT OUTER JOIN (SELECT h.* from patrimonio.historico_bem as h, patrimonio.historico_bem as uhb where h.cod_bem = uhb.cod_bem group by h.cod_bem, h.cod_situacao, h.cod_local, h.cod_setor,h.cod_departamento, h.cod_unidade, h.cod_orgao, h.ano_exercicio, h.timestamp, h.descricao having h.timestamp = max(uhb.timestamp)) as hb ON  \n";
$sSQLPDF .="       hb.cod_bem = b.cod_bem  \n";
$sSQLPDF .="       JOIN administracao.local as lo ON   \n";
$sSQLPDF .="            lo.ano_exercicio    = hb.ano_exercicio    AND  \n";
$sSQLPDF .="            lo.cod_orgao        = hb.cod_orgao        AND  \n";
$sSQLPDF .="            lo.cod_unidade      = hb.cod_unidade      AND  \n";
$sSQLPDF .="            lo.cod_departamento = hb.cod_departamento AND  \n";
$sSQLPDF .="            lo.cod_setor        = hb.cod_setor        AND  \n";
$sSQLPDF .="            lo.cod_local        = hb.cod_local         \n";
$sSQLPDF .="               INNER JOIN administracao.setor as se ON  \n";
$sSQLPDF .="                     se.ano_exercicio    = lo.ano_exercicio    AND  \n";
$sSQLPDF .="                     se.cod_orgao        = lo.cod_orgao        AND  \n";
$sSQLPDF .="                     se.cod_unidade      = lo.cod_unidade      AND  \n";
$sSQLPDF .="                     se.cod_departamento = lo.cod_departamento AND  \n";
$sSQLPDF .="                     se.cod_setor        = lo.cod_setor   \n";
$sSQLPDF .="                        INNER JOIN administracao.departamento as de ON  \n";
$sSQLPDF .="                              de.ano_exercicio    = se.ano_exercicio    AND  \n";
$sSQLPDF .="                              de.cod_orgao        = se.cod_orgao        AND  \n";
$sSQLPDF .="                              de.cod_unidade      = se.cod_unidade      AND  \n";
$sSQLPDF .="                              de.cod_departamento = se.cod_departamento   \n";
$sSQLPDF .="                                 INNER JOIN administracao.unidade as un ON  \n";
$sSQLPDF .="                                       un.ano_exercicio    = se.ano_exercicio    AND  \n";
$sSQLPDF .="                                       un.cod_orgao        = se.cod_orgao        AND  \n";
$sSQLPDF .="                                       un.cod_unidade      = se.cod_unidade        \n";
$sSQLPDF .="                                          JOIN administracao.orgao as org ON  \n";
$sSQLPDF .="                                               org.ano_exercicio    = un.ano_exercicio    AND  \n";
$sSQLPDF .="                                               org.cod_orgao        = un.cod_orgao   \n";
$sSQLPDF .="      LEFT JOIN patrimonio.bem_comprado as bc on ( hb.cod_bem = bc.cod_bem)  \n";
$sSQLPDF .="      inner join orcamento.entidade as oe on (bc.cod_entidade = oe.cod_entidade and bc.exercicio = oe.exercicio)  \n";
$sSQLPDF .="  WHERE  \n";
$sSQLPDF .="         oe.numcgm = $codEntidade  \n";
$sSQLPDF.= "   AND b.dt_aquisicao  between  '$dataInicial'  and '$dataFinal'                                      \n";
$sSQLPDF .="         $stWhere                  \n";
$sSQLPDF .="  GROUP BY  \n";
$sSQLPDF .="       b.cod_natureza  \n";
$sSQLPDF .="      ,b.cod_grupo  \n";
$sSQLPDF .="      ,b.cod_especie  \n";
$sSQLPDF .="      ,bc.exercicio    \n";
$sSQLPDF .="      ,b.cod_bem  \n";
$sSQLPDF .="      ,b.descricao  \n";
$sSQLPDF .="      ,bc.cod_empenho  \n";
$sSQLPDF .="      ,bc.exercicio  \n";
$sSQLPDF .="      ,lo.nom_local  \n";
$sSQLPDF .="      $stOrder       \n";

    $botoesPDF = new botoesPdfLegado;

    $botoesPDF->imprimeBotoes('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/relatorioBemEntidade.xml',$sSQLPDF,'','');
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
