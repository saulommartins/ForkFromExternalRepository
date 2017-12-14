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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: leandro.zis $
    $Date: 2007-06-26 14:11:26 -0300 (Ter, 26 Jun 2007) $

    * Casos de uso: uc-03.05.20
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function geraArquivosRegContabeis(&$obExportador , $stDataInicial, $stDataFinal)
{
    $sessao = $_SESSION ['sessao'];
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"  );
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"  );
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php"  );

    $obTOrcamentoReceita = new TOrcamentoReceita;
    $obTOrcamentoReceita->setDado( 'exercicio'   , $sessao->filtro['stExercicio'] );
    $obTOrcamentoReceita->setDado( 'cod_entidade', implode(",", $sessao->filtro['inCodEntidade'] ) );
    $obTOrcamentoReceita->setDado( 'dt_inicial'  , $stDataInicial );
    $obTOrcamentoReceita->setDado( 'dt_final'  , $stDataFinal );
    $obTOrcamentoReceita->recuperaReceitaArrecadada( $rsReceitaArrecadada);

    $obExportador->addArquivo("receitaarrecadada.txt");
    $obExportador->roUltimoArquivo->addBloco($rsReceitaArrecadada);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_criacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_nat_receita");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_original");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
