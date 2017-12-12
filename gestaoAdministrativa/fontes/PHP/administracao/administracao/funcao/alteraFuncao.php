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
* Manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.03.97
*/

include '../../includes/cabecalho.php';
if (!(isset($ctrl)))
$ctrl = 0;
switch ($ctrl) {
case 0:

if (isset($acao)) {

            $sSQLs = "SELECT cod_funcao, nom_funcao FROM ".FUNCAO;
            #sessao->transf = $sSQLs;
            Sessao::write('sSQLs',$sSQLs);
    }
        include '../../classes/paginacao.class.php';
        $paginacao = new paginacao;
        #$paginacao->pegaDados(sessao->transf,"12");
        $paginacao->pegaDados(Sessao::read('sSQLs'),"12");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_funcao","DESC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBase;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "<table width=450><tr><td class=alt_dados colspan=2>Função</td></tr>";
        while (!$dbEmp->eof()) {
                $codFuncaof  = trim($dbEmp->pegaCampo("cod_funcao"));
                $nomFuncaof  = trim($dbEmp->pegaCampo("nom_funcao"));
                $dbEmp->vaiProximo();
                $exec .= "<tr><td class=show_dados>".$nomFuncaof."</td><td class=show_dados width=20><a href='alteraFuncao.php?".Sessao::getId()."&codFuncao=".$codFuncaof."&ctrl=1'><img src='../../images/btneditar.gif' border=0></a></td></tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";

break;
case 1:
//****************************************************************************
$sSQL = "select cod_funcao, nom_funcao FROM sw_funcao WHERE cod_funcao = ".$codFuncao;
$dbEmp = new dataBase;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$codFuncao  = trim($dbEmp->pegaCampo("cod_funcao"));
$nomFuncao  = trim($dbEmp->pegaCampo("nom_funcao"));
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
//****************************************************************************
?>
      <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomFuncao.value.length;
            if (campo == 0) {
            mensagem += "@O Campo Nome da Função é obrigatório";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
         function Cancelar()
         {
            mudaTelaPrincipal('<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=0&pagina=<?=$pagina;?>');
      }

   </script>

<form name="frm" action="alteraFuncao.php?<?=Sessao::getId();?>" method="POST">

<table width=450>

<tr>
<td colspan=2 class="alt_dados">Altere os dados da Função</td>
</tr>

<tr>
<td class=label>Nome da Classificação</td>
<td class=field><input type="text" name="nomFuncao" size=50 value="<?=$nomFuncao;?>">
<input type="hidden" name="codFuncao" value="<?=$codFuncao;?>">
<input type="hidden" name="ctrl" value=2>
</td>
</tr>

<tr>
<td class=field colspan=2>
<input type="button" name="Alterar" value="Alterar" onClick="Salvar();">&nbsp;
<input type="button" name="Cancelar" value="Cancelar" onClick="Cancelar();">&nbsp;
</td>
</tr>

</table>

</form>

<?php
break;
case 2:
    include '../../classes/configuracao.class.php';
    $config = new configuracao;
    $config->setaValorFuncao($codFuncao,$nomFuncao);
    if ($config->updateFuncao()) {
                    include '../../classes/auditoria.class.php';
                    $audicao = new auditoria;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomFuncao);
                    $audicao->insereAuditoria();
                    echo '<script type="text/javascript">
                    alertaAviso("'.$nomFuncao.'","alterar","aviso","'.Sessao::getId().'");
                    window.location = "alteraFuncao.php?'.Sessao::getId().'";
                    </script>';
                    } else {
                    echo '<script type="text/javascript">
                    alertaAviso("'.$nomFuncao.'","n_alterar","aviso","'.Sessao::getId().'");
                    window.location = "alteraFuncao.php?'.Sessao::getId().'";
                    </script>';
                    }

break;
}
include '../../includes/rodape.php';
?>
