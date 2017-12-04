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
* Arquivo de instância para Questões
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

* Casos de uso: uc-01.07.94
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';

    if (!(isset($ctrl))) {
        $ctrl = 0;
    }

    switch ($ctrl) {
        case 0:
        $anoExercicio = pegaConfiguracao("ano_exercicio");
?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomQuestao.value;
            if (campo == "") {
            mensagem += "@Campo Questão inválido!()";
            erro = true;
        }

        campo = document.frm.ordemQuestao.value;
            if (campo == "") {
            mensagem += "@Campo Ordem inválido!()";
            erro = true;
        }

        campo = document.frm.ordemQuestao.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Ordem inválido!("+campo+")";
            erro = true;
        }

        campo = document.frm.tipo.value;
            if (campo == "xxx") {
            mensagem += "@Campo Tipo inválido!()";
            erro = true;
        }

        campo = document.frm.anoExercicio.value;
            if (campo == "") {
            mensagem += "@Campo Exercício inválido!()";
            erro = true;
        }

        campo = document.frm.anoExercicio.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Exercício inválido!("+campo+")";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id?>','');
                return !(erro);
      }

      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
</script>
<form action="incluiQuestao.php?<?=$sessao->id?>&ctrl=1" method="POST" name="frm">
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">
            Dados da Questão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Descrição da questão">
            *Questão
        </td>
        <td class="field">
            <input type="text" name="nomQuestao" value="<?=$nomQuestao?>" size="60" maxlength="160">
        </td>
    </tr>
    <tr>
        <td class="label" title="Ordem da questão">
            *Ordem
        </td>
        <td class="field">
            <input type="text" name="ordemQuestao" value="<?=$ordemQuestao?>" size="4" maxlength=4>
        </td>
    </tr>
    <tr>
        <td class="label" title="Ano exercício da questão">
            *Exercício
        </td>
        <td class="field">
            <input type="text" name="anoExercicio" value="<?=$anoExercicio?>" size="4" maxlength="4">
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo da quetão">
            *Tipo
        </td>
        <td class="field">
            <select name="tipo">
                <option value="xxx">Selecione</option>
                <option value="t">Texto</option>
                <option value="n">Número</option>
                <option value="l">Lista</option>
                <option value="m">Lista Múltipla</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Valores padrão para as respostas">
            Valor Padrão
        </td>
        <td class="field">
            <textarea name="valorPadrao" cols="30" rows="6"></textarea>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <?php geraBotaoOk();?>
        </td>
    </tr>
</table>
</form>

<?php
    break;
        case 1:
            $var = array(
            codQuestao=>$codQuestao,
            anoExercicio=>$anoExercicio,
            nomQuestao=>$nomQuestao,
            ordemQuestao=>$ordemQuestao,
            tipo=>$tipo,
            valorPadrao=>$valorPadrao
            );
            $incluir = new cse;
            if ($incluir->incluiQuestao($var)) {
                include(CAM_FW_LEGADO."auditoriaLegada.class.php");
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $nomQuestao);
                $audicao->insereAuditoria();
                echo '<script type="text/javascript">
                    alertaAviso("'.$nomQuestao.'","incluir","aviso","'.$sessao->id.'","");
                    window.location = "incluiQuestao.php?'.$sessao->id.'";
                    </script>';
            } else {
                echo '<script type="text/javascript">
                    alertaAviso("'.$nomQuestao.'","n_incluir","erro","'.$sessao->id.'","");
                    window.location = "incluiQuestao.php?'.$sessao->id.'";
                    </script>';
            }
    break;
    }
    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
