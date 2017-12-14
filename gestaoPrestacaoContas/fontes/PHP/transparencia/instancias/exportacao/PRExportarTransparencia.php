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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

# Conexão com o servidor FTP.
$conecta = ftp_connect("ftp.bsb.cnm.org.br");

if (!$conecta) die('Erro ao conectar com o servidor');

# Loga no servidor FTP.
$login = ftp_login($conecta, 'transparencia_urbem', '6XCGRrTm');

if (!$login) die('Erro ao autenticar');

$pacoteZip  = Sessao::read('arArquivosDownload');
$nomePacote = $pacoteZip[0]['stNomeArquivo'];

$stHashIdentificador = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'hash_identificador'");

# Envia o arquivo, adicionado modo BINARY para não conflitar o pacote .zip
$enviaPacote = ftp_put($conecta, $stHashIdentificador."/remessas/".$nomePacote, CAM_GPC_TRANSPARENCIA_ARQUIVOS.$nomePacote, FTP_BINARY);

if (!$enviaPacote) {
    die('Erro ao enviar o pacote de arquivos');
} else {
    # Remove o pacote enviado do Urbem.
    unlink(CAM_GPC_TRANSPARENCIA_ARQUIVOS.$nomePacote);
    echo '<br /><br /><strong>PACOTE DE ARQUIVOS ENVIADO COM SUCESSO!</strong>';
}

# Desconecta do servidor
ftp_close($conecta);

?>
