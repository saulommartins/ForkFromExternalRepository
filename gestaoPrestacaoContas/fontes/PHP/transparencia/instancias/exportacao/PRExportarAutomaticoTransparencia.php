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

# Recupera o path_urbem escrito na Cron.
$stPathUrbem = shell_exec("sudo crontab -l -uwww-data|grep urbem-path|cut -f2 -d:");
$stPathUrbem = trim($stPathUrbem);

# Acessa o diretório do arquivo que deve ser executado: PRExportarAutomaticoTransparencia.php
chdir($stPathUrbem."/gestaoPrestacaoContas/fontes/PHP/transparencia/instancias/exportacao/");

# Geração automática do Pacote para o Portal da Transparência
$paramUsuario = "transparencia";
$paramSenha   = "suporte";
$stExercicio  = date("Y");

include_once $stPathUrbem.'/config.php';
include_once $stPathUrbem.'/gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once $stPathUrbem.'/gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once $stPathUrbem.'/gestaoAdministrativa/fontes/PHP/framework/URBEM/Sessao.class.php';

Sessao::open();
Sessao::setUsername($paramUsuario);
Sessao::setPassword($paramSenha);
Sessao::setExercicio($stExercicio);

$obConexao = new Conexao();
$obConexao->setUser($urbem_config['urbem']['connection']['username']);
$obConexao->setPassWord($urbem_config['urbem']['connection']['password']);

try {
    $obErro = $obConexao->abreConexao();
    if (!$obErro->ocorreu()) {
        $obErro = Sessao::consultarDadosSessao();

        if (!$obErro->ocorreu()) {
            $obErro = Sessao::verificarSistemaAtivo();

            if (!$obErro->ocorreu()) {

                # Verifica se o sistema está configurado para exportar/enviar automático.
                $boExportaAutomatico = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'exporta_automatico'");

                if ($boExportaAutomatico == "true") {
                    # Por padrão exporta dados de 1 de Janeiro até o dia atual.
                    $data = mktime (0, 0, 0, date("m"), date("d")-1, date("Y"));
                    $dtFinalEmissao = date('d/m/Y', $data);

                    $arFiltroRelatorio['stDataInicial'] = '01/01/'.$stExercicio;
                    $arFiltroRelatorio['stDataFinal']   = $dtFinalEmissao;
                    $arFiltroRelatorio['stExercicio']   = $stExercicio;

                    # Valores na sessão são usados na geração dos arquivos.
                    Sessao::write('filtroRelatorio', $arFiltroRelatorio);

                    # Gerar pacote.
                    include_once $stPathUrbem.'/gestaoPrestacaoContas/fontes/PHP/transparencia/instancias/exportacao/OCExportarTransparencia.php';

                    # Enviar pacote.
                    include_once $stPathUrbem.'/gestaoPrestacaoContas/fontes/PHP/transparencia/instancias/exportacao/PRExportarTransparencia.php';
                }

            } else {
                Sessao::close();
                echo $obErro->getDescricao();
            }

        } else {
            Sessao::close();
            echo $obErro->getDescricao();
        }

    } else {
        Sessao::close();
        echo "Erro ao logar no Urbem!";
    }

} catch (Exception $e) {
    echo '<strong>Erro ao logar:</strong> <p>' . $e->getMessage() . '</p>';
    Sessao::close();
}
