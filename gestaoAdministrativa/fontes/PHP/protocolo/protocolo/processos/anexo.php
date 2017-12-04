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
    * Página para validar download do anexop
    * Data de Criação   : 10/08/2004
    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @package URBEM

Casos de uso: uc-01.06.98

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"         );

    $selectCopia = 	"SELECT
                        imagem,
                        anexo
                    FROM
                        sw_copia_digital
                    WHERE
                        cod_processo = ".$_REQUEST['codProcesso']." AND
                        exercicio = '".$_REQUEST['anoExercicio']."' AND
                        cod_documento = ".$_REQUEST['codDoc']." AND
                        cod_copia = ".$_REQUEST['codCopia'];

    $dbCopia = new databaseLegado;
    $dbCopia->abreBd();
    $dbCopia->abreSelecao($selectCopia);
    $tipoAnexo = $dbCopia->pegaCampo("imagem");
    $anexo = $dbCopia->pegaCampo("anexo");

    $dbCopia->limpaSelecao();
    $dbCopia->fechaBd();

    $file = trim(CAM_PROTOCOLO."anexos/".$anexo);
    header('Content-Description: File Transfer');
    header('Content-Type: application/force-download');
    header('Content-Length: ' . filesize($file));
    header('Content-Disposition: attachment; filename=Anexo'.substr(basename($file),strrpos(basename($file),'.'),strlen(basename($file))) );
    readfile($file);
?>
    <script>
    function alertaAviso(objeto,tipo,chamada,sessao)
    {
        var x = 1;
        var y = 1;
        var sessaoid = sessao.substr(10,6);
        var sArq = '<?=CAM_FW_INSTANCIAS."mensagem.php";?>?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
        window.opener.parent.frames["telaMensagem"].location.replace(sArq);
    }
    </script>
    <?php
        print '<script type="text/javascript">
                 alertaAviso("Sem PERMISSÃO para download de anexo!","n_incluir","erro","'.Sessao::getId().'");
               </script>';
?>
