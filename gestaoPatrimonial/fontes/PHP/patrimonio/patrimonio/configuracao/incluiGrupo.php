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
    * Inclui os Grupos do Patrimônio
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.04
*/

/*
$Log$
Revision 1.31  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.30  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.29  2006/07/13 14:20:17  fernando
Alteração de hints

Revision 1.28  2006/07/13 14:17:30  fernando
Alteração de hints

Revision 1.27  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.26  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once 'JSIncluiGrupo.js';
setAjuda("UC-03.01.04");
$anoE = Sessao::getExercicio();

// operacoes no frame oculto
switch ($controle) {
    // busca a seguradora a partir do codigo fornecido
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
        $aux[3] = geraDigito($aux[0]);

        // quebra o valor gerado aux[3] e verifica se o digito gerado pra aux[3] é igual ao digito
        // gerado para codPlanoDebito que é o aux[1]
        $aux_2 = explode("-", $aux[3]);
        // compara os dois digitos...
        //if ($aux_2[1] == $aux[1]) {

            // busca nome da conta atraves do cod_conta informado
            $sql = "
                SELECT
                    pc.nom_conta
                FROM
                    contabilidade.plano_analitica as pa
                   ,contabilidade.plano_conta     as pc
                WHERE
                        pa.cod_plano = ".$aux[0]."
                    And pc.exercicio = '".$anoE."'
                    And pc.exercicio = pa.exercicio
                    AND pa.cod_conta = pc.cod_conta
                    AND pa.exercicio = pc.exercicio
                ";

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
                    $js .= 'f.depreciacao.focus()';
                } else {
                    $erro_conta = 1;
                }

        //} else {
        //    $erro_conta = 1;
        //}

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

    // exibe formulario para insercao de Grupo
    case 0:
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
                    document.frm.depreciacao.value = '0';
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

        // funcao que busca Conta de Débito no frame oculto
        function busca_conta(cod)
        {
            var f = document.frm;
            f.target = 'oculto';
            f.controle.value = cod;
            f.submit();
        }

        // desabilita botao 'OK' se o codNatureza informado nao existir e vice-versa
        function verificaNatureza(campo_a, campo_b)
        {
            var aux;
            aux = preencheCampo(campo_a, campo_b, '<?=Sessao::getId()?>');
            if (aux == false) {
                campo_a.value = '';
                document.frm.ok.disabled = true;
            } else {
                document.frm.ok.disabled = false;
            }
        }

    </script>

    <form name="frm" action="incluiGrupo.php?<?=Sessao::getId()?>" method="POST">

    <input type='hidden' name='controle' value="">
    <input type="hidden" name="ctrl" value=1>
    <input type="hidden" name="anoE" value="<?=$anoE;?>">

    <table width="100%">

    <tr>
        <td colspan="2" class="alt_dados">Dados do Grupo</td>
    </tr>

    <tr>
        <td class="label" title="Selecione a natureza do bem." width="20%">*Natureza</td>
        <td class="field" width="80%">

            <input type="text" name="codTxtNatureza" value="<?=$codNatureza != "xxx" ? $codNatureza : "";?>"
                size="10" maxlength="10" onChange="javascript: verificaNatureza(this, document.frm.codNatureza);"
                onKeyPress="return(isValido(this, event, '0123456789'));">

            <select name="codNatureza" onchange="verificaNatureza(this, document.frm.codTxtNatureza);">
                <option value="xxx" SELECTED>Selecione</option>
<?php
                $sSQL = "SELECT * FROM patrimonio.natureza ORDER by nom_natureza";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboNatureza = "";
                while (!$dbEmp->eof()) {
                    $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                    $nomNatureza  = trim($dbEmp->pegaCampo("nom_natureza"));
                    $dbEmp->vaiProximo();
                    $comboNatureza .= "<option value=".$codNaturezaf;
                        if (isset($codNatureza)) {
                            if ($codNaturezaf == $codNatureza)
                                $comboNatureza .= " SELECTED";
                        }
                    $comboNatureza .= ">".$nomNatureza."</option>\n";
                }
                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
                echo $comboNatureza;

?>
            </select>
        </td>
    </tr>

    <tr>
        <td class="label" title="Selecione a descrição do grupo do bem.">*Descrição do Grupo</td>
        <td class=field>
            <input type="text" name="nomGrupo" size="80" maxlength="80" value="<?=$nomGrupo;?>">
        </td>
    </tr>

    <tr>
        <td class='label'title="Informe a conta do plano de contas.">*Conta Contábil</td>
        <td class='field' valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">

            <tr>
            <td align="left" width="11%" valign="top">
                <input type='text' id='codPlanoDebito' name='codPlanoDebito'
                value='<?=$codPlanoDebito;?>' size='10' maxlength='10' onChange="busca_conta(1);"
                onKeyPress="return(isValido(this, event, '0123456789-'))">
                <input type="hidden" name="nomConta" value="<?=$nomConta;?>">
            </td>
            <td width="1">&nbsp;</td>
            <td align="left" width="63%" id="nomConta" class="fakefield" valign="middle">&nbsp;</td>

            <td align="left" valign="top">
                &nbsp;
                <a
href="javascript:abrePopUp('../../../../../../gestaoFinanceira/fontes/PHP/contabilidade/popups/planoConta/FLPlanoConta.php','frm','codPlanoDebito','nomConta','contaSinteticaAtivoPermanente','<?php echo Sessao::getId()?>','800','550');">
                <img  title="Buscar conta"  src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" border="0" align="absmiddle">
                </a>
            </td>
            </tr>

            </table>
        </td>
    </tr>

    <tr>
        <td class="label" title="Informe o percentual de depreciação.">Depreciação</td>
        <td class=field title="Informe uma porcentagem.">
<?php
            geraCampoMoeda( $sNome = "depreciacao", $maxLength = 5, $decimais = 2, $value = "$depreciacao", $boReadOnly = false, $sFuncao = "", 10);
?>
        % </td>
    </tr>

    <tr>
        <td colspan='2' class='field'>
            <?=geraBotaoOk2();?>
        </td>
    </tr>

    </table>

    </form>

<?php

    break;

    // insere grupo no BD
    case 1:

    // verifica se ja existe algum Grupo cadastrado com o nome de Grupo informado
    if (comparaValor("nom_grupo", $nomGrupo,"patrimonio.grupo","and cod_natureza = $codNatureza ",1)) {

    // se não existir nenhum igual
        include_once '../configPatrimonio.class.php';
        $objeto = "Grupo: ".$nomGrupo;
        $nId = pegaID("cod_grupo","patrimonio.grupo", "WHERE  cod_natureza = $codNatureza");

        // pega somente o cod_plano e despreza o "-" e o "digito"
        // ex: 2-7 (pega somente o 2)
        $codPlano = explode("-", $codPlanoDebito);

        // troca virgula decimal por ponto decimal e retira os pontos separadores de milhar

        if ( trim($depreciacao) == '' ) { $depreciacao = '0';}

        $depreciacao = str_replace(',','.',str_replace('.','',$depreciacao));

        if ($depreciacao > 100.00) {
                echo '
                <script type="text/javascript">
                    alertaAviso("Depreciação não pode ser superior a 100%","n_incluir","aviso","'.Sessao::getId().'");
                    mudaTelaPrincipal("incluiGrupo.php?'.Sessao::getId().'&codNatureza='.$codNatureza.'");
               </script>';
        } else {
            $patrimonio = new configPatrimonio;
            $patrimonio->setaVariaveisGrupo($nId, $codNatureza, $nomGrupo, $anoE, $depreciacao);

            if ($patrimonio->insereGrupo()) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';

                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
                $audicao->insereAuditoria();

                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$objeto.'","incluir","aviso","'.Sessao::getId().'");
                        mudaTelaPrincipal("incluiGrupo.php?'.Sessao::getId().'");
                    </script>';

             } else {

                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$insert.'","n_incluir","erro","'.Sessao::getId().'");
                        mudaTelaPrincipal("incluiGrupo.php?'.Sessao::getId().'");
                    </script>';
                }

        }//Se já existir algum registro com esse nome
        } else {
            echo '
                <script type="text/javascript">
                    alertaAviso("Já existe um Grupo com esse nome","unica","erro","'.Sessao::getId().'");
                    window.location = "incluiGrupo.php?'.Sessao::getId().'&nomGrupo='.$nomGrupo.'&codNatureza='.$codNatureza.'";
                </script>';
        }

        break;

}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
