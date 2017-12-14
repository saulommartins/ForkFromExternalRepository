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

/**
    * Página de Include Oculta - Exportação Arquivos TCMBA - Salario2.txt

    * Data de Criação   : 23/10/2015

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: Salario2.inc.php 63946 2015-11-10 21:10:32Z michel $
*/

include_once CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio().'/TTCMBASalario2.class.php';

$obTTCMBASalario2 = new TTCMBASalario2();
$obTTCMBASalario2->setDado('entidades'      , $stEntidades                      );
$obTTCMBASalario2->setDado('entidade_rh'    , $stEntidadeRH                     );
$obTTCMBASalario2->setDado('competencia'    , Sessao::getExercicio().$inMes     );
$obTTCMBASalario2->setDado('unidade_gestora', $inCodUnidadeGestora              );
$obTTCMBASalario2->setDado('mes_ano'        , $inMes.Sessao::getExercicio()     );
$obTTCMBASalario2->setDado('exercicio'      , Sessao::getExercicio()            );

#Teste de Erro
if($arFiltro['stInformativoErro']=='Sim'){
    $obTTCMBASalario2->recuperaLogErro($rsLogErro);

    //Variável $boLogErro para se houver algum erro que impeça a geração do arquivo
    $boLogErro = false;
    if( !$rsLogErro->eof( )){
        $arLogErrosTemp = array();
        $inRegistros = $rsLogErro->getCampo("registros");
        $inObrigatorio1 = $rsLogErro->getCampo("obrigatorio1");
        $inObrigatorio2 = $rsLogErro->getCampo("obrigatorio2");
        $inObrigatorio3 = $rsLogErro->getCampo("obrigatorio3");
        $inObrigatorio4 = $rsLogErro->getCampo("obrigatorio4");
        $inObrigatorio5 = $rsLogErro->getCampo("obrigatorio5");
        $inObrigatorio6 = $rsLogErro->getCampo("obrigatorio6");
        $inObrigatorio7 = $rsLogErro->getCampo("obrigatorio7");
        $inObrigatorio8 = $rsLogErro->getCampo("obrigatorio8");
        $inObrigatorio9 = $rsLogErro->getCampo("obrigatorio9");
        $inObrigatorio10 = $rsLogErro->getCampo("obrigatorio10");
        $inObrigatorio11 = $rsLogErro->getCampo("obrigatorio11");
        $inObrigatorio12 = $rsLogErro->getCampo("obrigatorio12");

        if( $inRegistros > 0 ){
            if( $inObrigatorio1 == 0 ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório informar o Salário Base TCM-BA em:                                   Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio2 == 0 ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório informar as Demais Vantagens Salariais TCM-BA em:                    Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';
 
                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio3 == 0 ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório informar a Gratificação de Função TCM-BA em:                         Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio4 == 0 ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório informar o Salário Família TCM-BA em:                                Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio5 == 0 ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório informar as Horas Extras TCM-BA em:                                  Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio6 == 0 ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório informar os Demais Descontos TCM-BA em:                              Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio7 == 0 ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório informar o Plano de Saúde/Odontológico TCM-BA em:                    Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio8 <> $inRegistros ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório configurar a Lotação do Servidor com Órgão Orçamentário TCM-BA em:   Gestão Prestação de Contas :: TCM - BA :: Configuração :: Relacionar Lotações/Orgãos';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio9 <> $inRegistros ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório configurar o Tipo de Cargo TCM-BA em:                                Gestão Prestação de Contas :: TCM - BA :: Configuração :: Relacionar Tipo de Cargo';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio10 <> $inRegistros ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório configurar o Tipo Função do Servidor TCM-BA em:                      Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio11 <> $inRegistros ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório configurar a Classe/Aplicação do Salário do Servidor TCM-BA em:      Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
            if( $inObrigatorio12 <> $inRegistros ){
                $arLogErrosTemp['nome_arquivo'] = $stArquivo;
                $arLogErrosTemp['erro'] = 'Obrigatório configurar a Função Servidor Temporário TCM-BA em:                   Gestão Prestação de Contas :: TCM - BA :: Configuração :: Configurar Tipos de Salários';

                $arLogErros[] = $arLogErrosTemp;
            }
        }

        unset($arLogErrosTemp);
    }
    unset($rsLogErro);
}
#Fim Teste de Erro

$rsSalario2 = new RecordSet();
if( !$boLogErro )
    $obTTCMBASalario2->recuperaDados($rsSalario2,"","",$boTransacao);

$obExportador->roUltimoArquivo->addBloco($rsSalario2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_gestora");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_cargo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("funcao_atual");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("classe");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cargo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_base");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_vantagens");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_gratificacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_familia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_ferias");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_horas_extra");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_decimo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_descontos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_irrf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_irrf_decimo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_consignado_1");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_consignado_2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_consignado_3");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_previdencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_irrf_ferias");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_previdencia_decimo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_previdencia_ferias");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_pensao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desconto_plano_saude");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_liquido");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_dias");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_mensais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_mensais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_banco_1");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_banco_2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_banco_3");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("competencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("folha");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao_temporario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

unset($obTTCMBASalario2);
unset($rsSalario2);

?>
