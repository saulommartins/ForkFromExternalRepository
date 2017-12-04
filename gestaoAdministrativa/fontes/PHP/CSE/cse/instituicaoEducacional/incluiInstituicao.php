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
* Arquivo de instância para Instituição Educacional
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19055 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 08:44:42 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.87
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    $codInstituicao = pegaID('cod_instituicao',"cse.instituicao_educacional");
    if (!(isset($ctrl))) {
        $ctrl = 0;
    }

    switch ($ctrl) {
        case 0:
?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomInstituicao.value;
            if (campo == "") {
            mensagem += "@Campo Nome da Instituição inválido!()";
            erro = true;
         }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id?>');
                return !(erro);
      }

      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
</script>
<form action="incluiInstituicao.php?<?=$sessao->id?>&ctrl=1" method="POST" name="frm">
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">
            Instituição
        </td>
    </tr>
<input type="hidden" name="codInstituicao" value="<?=$codInstituicao;?>">
    <tr>
        <td class="label" width="20%" title="Nome da instituição">
            *Nome
        </td>
        <td class="field" width="80%">
            <input type="text" name="nomInstituicao" value="<?=$nomInstituicao?>" size="30" maxlength="160">
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <?php
            geraBotaoOk();
            ?>
        </td>
    </tr>
</table>

</form>

<?php
    break;
        case 1:
        $var = array(
        codInstituicao=>$codInstituicao,
        nomInstituicao=>addslashes($nomInstituicao)
        );
        $incluir = new cse;
        if (comparaValor("nom_instituicao", $var[nomInstituicao], "cse.instituicao_educacional","",1)) {
            if ($incluir->incluiInstituicao($var)) {
                include CAM_FW_LEGADO."auditoriaLegada.class.php";
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $var[nomInstituicao]);
                $audicao->insereAuditoria();
                echo '
                    <script type="text/javascript">
                    alertaAviso("'.$var[nomInstituicao].'","incluir","aviso","'.$sessao->id.'");
                    mudaTelaPrincipal("'.$PHP_SELF.'?'.$sessao->id.'");
                    </script>';
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("'.$var[nomInstituicao].'","n_incluir","erro","'.$sessao->id.'");
                    mudaTelaPrincipal("'.$PHP_SELF.'?'.$sessao->id.'");
                    </script>';
            }
        } else {
            echo '
                <script type="text/javascript">
                alertaAviso("A Instituição '.$var[nomInstituicao].' já existe","unica","erro","'.$sessao->id.'");
                mudaTelaPrincipal("incluiInstituicao.php?'.$sessao->id.'&codInstituicao='.$var[codInsituicao].'&nomInstituicao='.$var[nomInstituicao].'&ctrl=0");
                </script>';
        }
    break;
    }
    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
