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
* Manutneção do sistema
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.03.91
*/

     include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
     include (CAM_FW_LEGADO."funcoesLegado.lib.php");
     include (CAM_FW_LEGADO."sistema.class.php");
     include (CAM_FW_LEGADO."emailLegado.class.php");
    $solicita = new sistema;
    $solicita->selecionaPrefeitura();
    $mLista = $solicita->selecionaModulo();
    $solicita->destinatario = "suporte@cnm.org.br";
    $oEmail = new emailLegado;
?>
    <form action="<?=$PHP_SELF;?>?<?=Sessao::getId();?>" method="POST" name="frm">
        <table width=100%>
            <tr>
                <td class=alt_dados colspan=2>
                    Solicitação de Suporte
                </td>
            </tr>
            <tr>
                <td class=label>
                    Prefeitura
                </td>
                <td class=field>
                    <?php echo "<b>".$solicita->prefeitura."</b>"; ?>
                </td>
            </tr>
            <tr>
                <td class=label>
                    De
                </td>
                <td class=field>
                    <b><?php echo $oEmail->selecionaRemetente(); ?></b>
                </td>
            </tr>
            <tr>
                <td class=label>
                    Cópia para
                </td>
                <td class=field>
                    <input type="text" name="sCopia" size=30 maxlength=60 value="<?=$sCopia?>">
                </td>
            </tr>
            <tr>
                <td class=label>
                    Módulo
                </td>
                <td class=field>
                    <select name="modulo" style="width: 200px;" onChange="document.frm.submit();">
                        <option value="">Selecione uma Opção</option>
                            <?php
                                while (list ($key, $val) = each ($mLista)) {
                                    if ($key == $modulo)
                                        print "<option value='$key' selected>$val</option>"; //Cria a combo com o nome dos Módulos>
                                    else
                                        print "<option value='$key'>$val</option>"; //Cria a combo com o nome dos Módulos>
                                }

                            ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class=label>
                    Funcionalidade
                </td>
                <td class=field>
                    <select name="funcionalidade" style="width: 200px;" onChange="document.frm.submit();">
                        <option value='0' selected> Selecione uma Opção</option>
                            <?php
                                if (isset($modulo)) {
                                    $fLista = $solicita->selecionaFuncionalidade($modulo);
                                    while (list ($key, $val) = each ($fLista)) {
                                        if ($key == $funcionalidade)
                                            print "<option value='$key' selected>$val</option>"; //Cria a combo com o nome dos Módulos>
                                        else
                                            print "<option value='$key'>$val</option>"; //Cria a combo com o nome dos Módulos>
                                    }
                                }
                            ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class=label>
                    Ação
                </td>
                <td class=field>
                    <select name="acao1" style="width: 200px;" onChange="document.frm.submit();">
                        <option value='0' selected> Selecione uma Opção</option>
                            <?php
                                if (isset($modulo)) {
                                    $aLista = $solicita->selecionaAcao($funcionalidade);
                                    while (list ($key, $val) = each ($aLista)) {
                                        if ($key == $acao1)
                                            print "<option value='$key' selected>$val</option>"; //Cria a combo com o nome dos Módulos>
                                        else
                                            print "<option value='$key'>$val</option>"; //Cria a combo com o nome dos Módulos>
                                    }
                                }
                            ?>
                    </select>
                </td>
            </tr>
            <?php
                if (isset($modulo)) {
                    $dbConfig = new dataBaseLegado;
                    $dbConfig->abreBd();
                    $select = "select nom_modulo from administracao.modulo where cod_modulo = '$modulo'";
                    $dbConfig->abreSelecao($select);
                    $solicita->modulo = $dbConfig->pegaCampo("nom_modulo");
                    $dbConfig->limpaSelecao;
                }
                if (isset($funcionalidade)) {
                    $dbConfig = new dataBaseLegado;
                    $dbConfig->abreBd();
                    $select = "select nom_funcionalidade from administracao.funcionalidade where cod_funcionalidade = '$funcionalidade'";
                    $dbConfig->abreSelecao($select);
                    if (!$dbConfig->eof())
                        $solicita->funcionalidade = $dbConfig->pegaCampo("nom_funcionalidade");
                    $dbConfig->limpaSelecao;
                }
                if (isset($acao1)) {
                    $dbConfig = new dataBaseLegado;
                    $dbConfig->abreBd();
                    $select = "select nom_acao from administracao.acao where cod_acao = '$acao1'";
                    $dbConfig->abreSelecao($select);
                    if (!$dbConfig->eof())
                        $solicita->acao1 = $dbConfig->pegaCampo("nom_acao");
                    $hidden = "0";
                    $dbConfig->limpaSelecao;
                }
                $solicita->corpo = "Prefeitura: $solicita->prefeitura\n
                Módulo: $solicita->modulo\n
                Funcionalidade: $solicita->funcionalidade\n
                Ação: $solicita->acao1\n";

            ?>
            <tr>
                <td class=label>
                    Descrição do Problema
                </td>
                <td class=field>
                    <textarea name="corpo" rows="10" cols="70"></textarea>
            <tr>
                <td class=field colspan=2>
                    <input type="submit" name="enviar" value="OK" style="width:60px">
                </td>
            </tr>
        </table>
<?php $solicita->assunto .= "$solicita->modulo"."/"."$solicita->funcionalidade"."/"."$solicita->acao1"."-"."$solicita->prefeitura";
    $solicita->corpo .= $corpo;
    if ($enviar) {
        $envia = new emaillegado;
        $envia->remetente = $envia->selecionaRemetente();
        $envia->destinatario = $solicita->destinatario;
        if (strlen($sCopia)>0) {
            $envia->destinatario .= ",".$sCopia;
        }
        $envia->assunto = $solicita->assunto;
        $envia->corpo = $solicita->corpo;
        if ($envia->remetente != "") {
            if ($envia->enviaEmail()) {
                 echo '<script type="text/javascript">
                 alertaAviso("Solicitação de Suporte","incluir","aviso","'.Sessao::getId().'");
                 window.location = "solicitaSuporte.php?'.Sessao::getId().'";
                 </script>';
            } else
                 echo '<script type="text/javascript">
                 alertaAviso("Solicitação de Suporte","n_incluir","erro","'.Sessao::getId().'");
                 window.location = "solicitaSuporte.php?'.Sessao::getId().'";
                 </script>';
        } else
                 echo '<script type="text/javascript">
                 alertaAviso("Solicitação de Suporte","n_incluir","erro","'.Sessao::getId().'");
                 window.location = "solicitaSuporte.php?'.Sessao::getId().'";
                 </script>';
    }

?>
    </form>

<?php
    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
