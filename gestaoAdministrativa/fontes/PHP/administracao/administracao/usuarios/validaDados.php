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
* Manutneção de usuários
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 15572 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 07:44:53 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.03.93
*/

include '../../includes/cabecalho.php';
include(CAM_FW_LEGADO."sistema.class.php");

setAjuda( "UC-01.03.93" );

switch ($ctrl) {
case 0: //Submit
    $js = "";
    $ok = true;
    $data = explode("/",$dataLote);
    $mes = $data[1];
    $ano = $data[2];
    if ($ano!=$exercicio) {
        $js .= "f.ok.disabled = false; \n";
        $js .= "erro = true; \n";
        $js .= "mensagem += '@O Ano da Data do Lote deve corresponder ao Exercício Atual (".$exercicio.")'; \n";
        $ok = false;
    }
    //Valida o dígito da conta a débito
    if (strlen($codPlanoDebito)>0) {
        if (!verificaDigito($codPlanoDebito)) {
            $ok = false;
            $js .= "mensagem += '@O Dígito da Conta a Débito não é Válido'; \n";
        }
        //Verifica se existe a conta a débito no plano de contas
        $valor = explode("-",$codPlanoDebito);
        if (comparaValor("cod_plano", $valor[0], "sw_plano_analitica","And exercicio = '".$exercicio."'")) {
            $ok = false;
            $js .= "mensagem += '@A Conta a Débito não existe'; \n";
        }
    }
    //Valida o dígito da conta a crédito
    if (strlen($codPlanoCredito)>0) {
        //Valida o dígito da conta a crédito
        if (!verificaDigito($codPlanoCredito)) {
            $ok = false;
            $js .= "mensagem += '@O Dígito da Conta a Crédito não é Válido'; \n";
        }
        //Verifica se existe a conta a crédito no plano de contas
        $valor = explode("-",$codPlanoCredito);
        if (comparaValor("cod_plano", $valor[0], "sw_plano_analitica","And exercicio = '".$exercicio."'")) {
            $ok = false;
            $js .= "mensagem += '@A Conta a Crédito não existe'; \n";
        }
    }

    //Gera o submit do form
    if ($ok) {
        $js .= "f.controle.value = ".$controle."; \n";
        if ($mes==$mesProc) {
            $js .= "f.submit(); \n";
        } else {
            $js .= 'alertaSubmit("O mês da Data do Lote é diferente do mês de Processamento('.$mesProc.'). Deseja continuar assim mesmo ?"); ';
            $js .= "f.ok.disabled = false; \n";
        }
    } else {
        $js .= "f.ok.disabled = false; \n";
        $js .= "erro = true; \n";
    }
    break;

case 1:
//Verifica se o setor digitado é válido. Caso seja retorna o nome do setor
    $js = "";
    $ok = true;
    $setor = validaSetor($chave,$exercicio);

    if ($setor) {
        $js .= "f.nomSetor.value = '".$setor[nomSetor]."'; \n";
    } else {
        $js .= "f.nomSetor.value = 'Setor Não Existe'; \n";
    }
    break;
}

?>
<html>
<head>
<script type="text/javascript">
function executa()
{
    var mensagem = "";
    var erro = false;
    var f = window.parent.frames["telaPrincipal"].document.frm;
    var d = window.parent.frames["telaPrincipal"].document;
    var aux;

    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
}
</script>

</head>

<body onLoad="javascript:executa();">

</body>

</html>
