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
    * Altera os Grupos do Patrimônio
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 23990 $
    $Name$
    $Autor: $
    $Date: 2007-07-13 17:49:09 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-03.01.04
*/

/*
$Log$
Revision 1.33  2007/07/13 20:49:09  rodrigo_sr
Bug#9627#

Revision 1.32  2007/06/26 19:22:38  rodrigo_sr
Bug#8328#

Revision 1.31  2007/01/03 11:26:36  hboaventura
correção de bug na alteração do Grupo

Revision 1.30  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.29  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.28  2006/07/13 14:17:29  fernando
Alteração de hints

Revision 1.27  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.26  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.04");
$anoE = Sessao::getExercicio();
// operacoes no frame oculto
switch ($controle) {
    case 1:
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/popupsLegado/planoConta/buscaPlanoConta.class.php';
        $aux = "";
        $erro_conta = "";
        $nomConta = "";

        $js = "f.controle.value = 0; \n";

        // quebra codPlanoDebito em duas variaveis
        // aux[0] = cod_conta
        // aux[1] = digito
        $aux = explode ("-", $codPlanoDebito);

        // gera um digito atraves do cod_conta informado aux[0]
//        $aux[3] = geraDigito($aux[0]);

        // quebra o valor gerado aux[3] e verifica se o digito gerado pra aux[3] é igual ao digito
        // gerado para codPlanoDebito que é o aux[1]
        $aux_2 = explode("-", $aux[3]);

        // compara os dois digitos...
//        if ($aux_2[1] == $aux[1]) {

            // busca nome da conta atraves do cod_conta informado
//            $sql = "
//                SELECT
//                    pc.nom_conta
//                FROM
//                    sw_plano_analitica as pa, sw_plano_conta as pc
//                WHERE
//                    pa.cod_plano = ".$aux[0]."
//                    And pc.exercicio = '".$anoE."'
//                    And pc.exercicio = pa.exercicio
//                    And pc.cod_nivel_1 = pa.cod_nivel_1
//                    And pc.cod_nivel_2 = pa.cod_nivel_2
//                    And pc.cod_nivel_3 = pa.cod_nivel_3
//                    And pc.cod_nivel_4 = pa.cod_nivel_4
//                    And pc.cod_nivel_5 = pa.cod_nivel_5
//                    And pc.cod_nivel_6 = pa.cod_nivel_6
//                    And pc.cod_nivel_7 = pa.cod_nivel_7
//                    And pc.cod_nivel_8 = pa.cod_nivel_8
//                    And pc.cod_nivel_9 = pa.cod_nivel_9
//                ";
              if (!($aux[0]==null || trim($aux[0])=="")) {
                $sql  = "SELECT pc.nom_conta                        \n";
                $sql .= "  FROM contabilidade.plano_analitica as pa \n";
                $sql .= "    ,contabilidade.plano_conta     as pc   \n";
                $sql .= "Where 1 = 1                                \n";
                $sql .= "And pa.cod_plano = ".$aux[0]."             \n";
                $sql .= "And pc.exercicio = '".$anoE."'             \n";
                $sql .= "And pc.exercicio = pa.exercicio            \n";
                $sql .= "And pc.cod_conta = pa.cod_conta            \n";

                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sql);
                $conn->vaiPrimeiro();

                while (!$conn->eof()) {
                    $nomConta  = trim($conn->pegaCampo("nom_conta"));
                    $conn->vaiProximo();
                }

                $conn->limpaSelecao();
                $conn->fechaBD();

                if (strlen($nomConta) > 0) {
                    $js .= 'd.getElementById("nomConta").innerHTML = "'.$nomConta.'";';
                    $js .= 'f.nomGrupo.focus()';
                } else {
                    $erro_conta = 1;
                }
              }

//        } else {
//            $erro_conta = 1;
//        }

        if ($erro_conta == 1) {
            $js .= 'f.codPlanoDebito.value = "" ;';
            $js .= 'd.getElementById("nomConta").innerHTML = "&nbsp;";';
            $js .= "erro = true;\n";
            $js .= 'mensagem += "Campo Conta Contábil inválido! (Código: '.$codPlanoDebito.').";';
            $js .= 'f.codPlanoDebito.focus()';
        }

        executaFrameOculto($js);

        exit();

        break;
}
// encerra operacoes no frame oculto

if (!(isset($ctrl)))
    $ctrl = 0;

switch ($ctrl) {

    // filtra natureza dos grupos a serem filtrados
    case 0:
?>
        <script type="text/javascript">

            // submete formulario
            function Salvar()
            {
                document.frm2.submit();
            }

            // desabilita botao 'OK' se o codNatureza informado nao existir e vice-versa
            function verificaNatureza(campo_a, campo_b)
            {
                var aux;

                aux = preencheCampo(campo_a, campo_b);

                if (aux == false) {
                    document.frm2.ok.disabled = true;

                } else {
                    document.frm2.ok.disabled = false;
                }
            }

        </script>

        <form name="frm2" action="alteraGrupo.php?<?=Sessao::getId()?>" method="POST">

            <input type="hidden" name="ctrl" value="1">

        <table width="100%">

        <tr>
            <td colspan="2" class="alt_dados">Filtrar Grupo</td>
        </tr>

        <tr>
            <td class="label" title="Selecione a natureza do bem." width="20%">*Natureza</td>
            <td class="field" width="80%">

                <input type="text" name="codTxtNatureza" value="<?=$codNatureza != "xxx" ? $codNatureza : "";?>"
                    size="10" maxlength="10" onChange="javascript: verificaNatureza(this, document.frm2.codNatureza);"
                    onKeyPress="return(isValido(this, event, '0123456789'));">

                <select name="codNatureza" onchange="verificaNatureza(this, document.frm2.codTxtNatureza);">
                    <option value="xxx" SELECTED>Selecione</option>
<?php
                    $sSQL = "SELECT * FROM patrimonio.natureza ORDER by nom_natureza";
                    $conn = new dataBaseLegado;
                    $conn->abreBD();
                    $conn->abreSelecao($sSQL);
                    $conn->vaiPrimeiro();
                    $comboNatureza = "";
                    while (!$conn->eof()) {
                        $codNaturezaf  = trim($conn->pegaCampo("cod_natureza"));
                        $nomNatureza  = trim($conn->pegaCampo("nom_natureza"));
                        $conn->vaiProximo();
                        $comboNatureza .= "<option value=".$codNaturezaf;
                            if (isset($codNatureza)) {
                                if ($codNaturezaf == $codNatureza)
                                    $comboNatureza .= " SELECTED";
                            }
                        $comboNatureza .= ">".$nomNatureza."</option>\n";
                    }
                    $conn->limpaSelecao();
                    $conn->fechaBD();
                    echo $comboNatureza;

?>
                </select>
            </td>
        </tr>

        <tr>
            <td colspan='2' class='field'>
                <?=geraBotaoOk();?>
            </td>
        </tr>

        </table>

        </form>
<?php
    break;

    // lista grupos cadastrados com ou sem filtro por natureza
    case 1:

        $stFiltro = "&filtroCodTxtNatureza=".$codTxtNatureza."&filtroCodNatureza=".$codNatureza;

        // se a variavel codNatureza estiver setada monta filtro para a consulta
        if ($codNatureza != 'xxx') {
            $filtrarNatureza = "and g.cod_natureza = ".$codNatureza;
        } else {
            $filtrarNatureza = "";
        }

        $sSQLs = "
                SELECT
                    g.cod_grupo, g.cod_natureza, g.nom_grupo, n.nom_natureza
                FROM
                    patrimonio.grupo as g, patrimonio.natureza as n
                WHERE
                    g.cod_natureza = n.cod_natureza
                    $filtrarNatureza
                ";

        if (!isset($pagina)) {
            $sessao->transf = $sSQLs;
        }

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento="&ctrl=1";
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_grupo","ASC");
        $sSQL = $paginacao->geraSQL();

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQL);

        if ( $pagina > 0 and $conn->eof() ) {
            $pagina--;
            $paginacao->pegaPagina($pagina);
            $paginacao->complemento="&ctrl=1";
            $paginacao->geraLinks();
            $paginacao->pegaOrder("nom_grupo","ASC");
            $sSQL = $paginacao->geraSQL();
            $conn->abreSelecao($sSQL);
        }

        $conn->vaiPrimeiro();

?>
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="6">Registros do Grupo</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%" rowspan="2">&nbsp;</td>
            <td class="labelcenter" width="45%" colspan="2">Natureza</td>
            <td class="labelcenter" width="45%" colspan="2">Grupo</td>
            <td class="labelcenter" width="1%" rowspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td class="labelcenter" width="10%">Código</td>
            <td class="labelcenter" width="35%">Descrição</td>
            <td class="labelcenter" width="10%">Código</td>
            <td class="labelcenter" width="35%">Descrição</td>
        </tr>
<?php
        $cont = $paginacao->contador();

        while (!$conn->eof()) {
                $codGrupof  = trim($conn->pegaCampo("cod_grupo"));
                $nomGrupof  = trim($conn->pegaCampo("nom_grupo"));
                $codNaturezaf  = trim($conn->pegaCampo("cod_natureza"));
                $nomNaturezaf  = trim($conn->pegaCampo("nom_natureza"));
                $conn->vaiProximo();
?>
                <tr>
                    <td class="labelcenter" width="5%"><?=$cont++;?></td>
                    <td class="show_dados_right">&nbsp;<?=$codNaturezaf;?></td>
                    <td class="show_dados">&nbsp;<?=$nomNaturezaf;?></td>
                    <td class="show_dados_right">&nbsp;<?=$codGrupof;?></td>
                    <td class="show_dados">&nbsp;<?=$nomGrupof;?></td>
                    <td class="botao" title="Alterar">
                        <a href='alteraGrupo.php?<?=Sessao::getId();?>&codNatureza=<?=$codNaturezaf;?>&codGrupo=<?=$codGrupof;?>&ctrl=2&pagina=<?=$pagina.$stFiltro;?>'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif'  border="0">
                        </a>
                    </td>
                </tr>
<?php
        }
?>
        </table>
<?php
        $conn->limpaSelecao();
        $conn->fechaBD();

        echo "<table width=100% align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";

    break;

    // formulario para alteracao do grupo selecionado
    case 2:

    $stFiltro = "&CodTxtNatureza=".$filtrocodTxtNatureza."&CodNatureza=".$filtrocodNatureza;

/*
        $sSQL = "
                SELECT
                    g.cod_natureza, g.nom_grupo, g.cod_grupo, n.nom_natureza,
                    ga.exercicio, ga.cod_plano, g.depreciacao
                FROM
                    patrimonio.grupo as g, patrimonio.natureza as n, patrimonio.grupo_plano_analitica as ga
                WHERE
                    g.cod_natureza = ".$codNatureza." AND
                    g.cod_natureza = ga.cod_natureza AND
                    g.cod_grupo = ".$codGrupo." AND
                    g.cod_grupo = ga.cod_grupo AND
                    ga.exercicio = ".$anoE." AND
                    g.cod_natureza = n.cod_natureza";
*/
        $sSQL ="SELECT g.cod_natureza  ,
                       g.nom_grupo     ,
                       g.cod_grupo     ,
                       n.nom_natureza  ,
                       ga.exercicio    ,
                       ga.cod_plano    ,
                       g.depreciacao
                  FROM patrimonio.natureza as n                     ,
                       patrimonio.grupo as g
                  LEFT JOIN patrimonio.grupo_plano_analitica as ga
                       ON   g.cod_grupo = ga.cod_grupo
                        AND g.cod_natureza = ga.cod_natureza
                        AND ga.exercicio   = ".$anoE."

                 WHERE g.cod_natureza = ".$codNatureza. "
                   AND g.cod_grupo    = ".$codGrupo.    "
                   AND g.cod_natureza = n.cod_natureza  ";

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQL);
        $conn->vaiPrimeiro();

        $codNatureza = trim($conn->pegaCampo("cod_natureza"));
        $nomGrupo  = trim($conn->pegaCampo("nom_grupo"));
        $codGrupo = trim($conn->pegaCampo("cod_grupo"));
        $nomNatureza  = trim($conn->pegaCampo("nom_natureza"));
        $anoE  = trim($conn->pegaCampo("exercicio"));

        $codPlanoDebitoTmp  = trim($conn->pegaCampo("cod_plano"));

        // monta codPlanoDebito com o digito verificador da conta

        $depreciacao  = trim($conn->pegaCampo("depreciacao"));
        //troca ponto decimal por virgula
        //$depreciacao = number_format($depreciacao, 2, ',', ' ');
        $depreciacao = str_replace('.',',',$depreciacao);

        $conn->limpaSelecao();
        $conn->fechaBD();

?>
        <script type="text/javascript">

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;

                campo = document.frm.codNatureza.value;
                    if (campo == "xxx") {
                    mensagem += "@O campo Natureza é obrigatório.";
                    erro = true;
                }

                campo = trim(document.frm.nomGrupo.value).length;
                    if (campo == 0) {
                    mensagem += "@O campo Descrição do Grupo é obrigatório.";
                    erro = true;
                }

                campo = document.frm.codPlanoDebito.value.length;
                    if (campo == 0) {
                    mensagem += "@O campo Conta Contábil é obrigatório.";
                    erro = true;
                }

                campo = document.frm.depreciacao.value.length;
                    if (campo == 0) {
                    mensagem += "@O campo Depreciação é obrigatório.";
                    erro = true;
                }

                    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                    return !(erro);
            }

            function Salvar()
            {
                if (Valida()) {
                    document.frm.submit();
                }
            }

            function Cancela()
            {
                mudaTelaPrincipal("alteraGrupo.php?<?=Sessao::getId();?>&ctrl=1&pagina=<?=$pagina.$stFiltro;?>");
            }

            //funcao que busca Conta de Débito no frame oculto
            function busca_conta(cod)
            {
                var f = document.frm;
                f.target = 'oculto';
                f.controle.value = cod;
                f.submit();
            }

        </script>

        <form name="frm" action="alteraGrupo.php?<?=Sessao::getId()?>" method="POST">

            <input type="hidden" name="codNatureza" value="<?=$codNatureza;?>">
            <input type="hidden" name="codGrupo" value="<?=$codGrupo;?>">
            <input type="hidden" name="anoE" value="<?=Sessao::getExercicio();?>">
            <input type="hidden" name="pagina" value="<?=$pagina;?>">
            <input type="hidden" name="ctrl" value="3">
            <input type="hidden" name="controle" value="3">

        <table width="100%">

        <tr>
            <td colspan="2" class="alt_dados">Dados do Grupo</td>
        </tr>

        <tr>
            <td class="label" width="20%">Natureza</td>
            <td class="field" width="80%"><?=$codNatureza;?> - <?=$nomNatureza;?></td>
        </tr>

        <tr>
            <td class="label">Código</td>
            <td class="field"><?=$codGrupo;?></td>
        </tr>

        <tr>
            <td class="label" title="Informe a descrição do grupo do bem.">*Descrição do Grupo</td>
            <td class=field>
                <input type="text" name="nomGrupo" size="80" maxlength="80" value="<?=$nomGrupo;?>">
            </td>
        </tr>

        <tr>
            <td class='label'title="Informe a conta no plano de contas.">*Conta Contábil</td>
            <td class='field' valign="top">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                <tr>
                <td align="left" width="11%" valign="top">
                    <input type='text' id='codPlanoDebito' name='codPlanoDebito'
                    value='<?echo  $codPlanoDebitoTmp;?>' size='10' maxlength='10' onChange="busca_conta(1);"
                    onKeyPress="return(isValido(this, event, '0123456789-'))">
                    <input type="hidden" name="nomConta" value="<?=$nomConta;?>">
                    <input type="hidden" name="boAnalitica" value="<?=($codPlanoDebitoTmp =="" | $codPlanoDebitoTmp == null)?"nao":"sim";?>">
                </td>
                <td width="1">&nbsp;</td>
                <td align="left" width="63%" id="nomConta" class="fakefield" valign="middle">&nbsp;</td>

                <td align="left" valign="top">
                    &nbsp;
<!--                    <a href="javascript:procuraPlanoConta('frm','nomConta','','codPlanoDebito','1','<?=Sessao::getId();?>','1');"> -->

                <a
href="javascript:abrePopUp('../../../../../../gestaoFinanceira/fontes/PHP/contabilidade/popups/planoConta/FLPlanoConta.php','frm','codPlanoDebito','nomConta','contaSinteticaAtivoPermanente','<?php echo Sessao::getId()?>','800','550');">
                    <img   src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif"  border="0" align="absmiddle" title='Buscar conta'>
                    </a>
                </td>
                </tr>

                </table>
            </td>
        </tr>

        <tr>
            <td class="label" title="Informe o percentual de depreciação.">*Depreciação</td>
            <td class=field>
<?php
                geraCampoMoeda( $sNome = "depreciacao", $maxLength = 6, $decimais = 2, $value = "$depreciacao",  $boReadOnly = false, $sFuncao = "", 10);
?>
                <b>%</b>
            </td>
        </tr>

        <tr>
            <td colspan='2' class='field'>
                <?=geraBotaoAltera();?>
            </td>
        </tr>

        </table>

        <script>
            busca_conta(1)
        </script>

        </form>

<?php
    break;

    // executa operacao de UPDATE no BD
    case 3:
        include_once '../configPatrimonio.class.php';
        $objeto = "Grupo: ".$nomGrupo;
        $patrimonio = new configPatrimonio;

        // pega somente o cod_plano e despreza o "-" e o "digito"
        // ex: 2-7 (pega somente o 2)
        $codPlano = explode("-", $codPlanoDebito);

        // troca virgula decimal por ponto decimal e retira os pontos separadores de milhar
        //$depreciacao = number_format($depreciacao, 2, '.', ' ');
        $depreciacao = str_replace(',','.',str_replace('.','',$depreciacao));
        if ($depreciacao > 100.00) {
                echo '
                <script type="text/javascript">
                    alertaAviso("Depreciação não pode ser superior a 100%","n_alterar","aviso","'.Sessao::getId().'");
                    mudaTelaPrincipal("alteraGrupo.php?'.Sessao::getId().'&codNatureza='.$codNatureza.'&pagina='.$pagina.'&ctrl=1");
               </script>';
        } else {

         if ($boAnalitica == "nao") {
          $boAnalitica = false;
         } else {
          $boAnalitica = true;
         }
            $patrimonio->setaVariaveisGrupo($codGrupo, $codNatureza, $nomGrupo, $codPlano[0], Sessao::getExercicio(), $depreciacao);
            if (comparaValor("nom_grupo", $nomGrupo,"patrimonio.grupo", "and cod_natureza = $codNatureza and cod_grupo <> $codGrupo",1)) {
              if ($patrimonio->updateGrupo($boAnalitica)) {
                    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
                    $audicao->insereAuditoria();
                    echo '
                        <script type="text/javascript">
                            alertaAviso("'.$objeto.'","alterar","aviso","'.Sessao::getId().'");
                            mudaTelaPrincipal("alteraGrupo.php?'.Sessao::getId().'&codNatureza='.$codNatureza.'&pagina='.$pagina.'&ctrl=1");
                        </script>';
                } else {
                    echo '
                        <script type="text/javascript">
                            alertaAviso("'.$objeto.'","n_alterar","aviso","'.Sessao::getId().'");
                            mudaTelaPrincipal("alteraGrupo.php?'.Sessao::getId().'&codNatureza='.$codNatureza.'&pagina='.$pagina.'&ctrl=1");
                        </script>';
                }

        } else {

            echo '
                <script type="text/javascript">
                    alertaAviso("O Grupo '.$nomGrupo.' já existe","unica","erro","'.Sessao::getId().'");
                    mudaTelaPrincipal("alteraGrupo.php?'.Sessao::getId().'&nomGrupo='.$nomGrupo.'&codNatureza='.$codNatureza.'&codGrupo='.$codGrupo.'&ctrl=2");
                </script>';
        }
        }
    break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
