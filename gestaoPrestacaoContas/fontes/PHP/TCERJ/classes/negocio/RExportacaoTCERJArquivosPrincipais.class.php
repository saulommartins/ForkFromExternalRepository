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
    * Classe de Exportação Arquivos Principais
    * Data de Criação   : 11/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Exportador

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:05  hboaventura
Ticket#10234#

Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoAlteracaoOrcamentaria.class.php"   );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoPlanoContas.class.php"             );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoDotacaoOrcamentaria.class.php"     );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoItensDespesa.class.php"            );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoItensReceita.class.php"            );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoEstorno.class.php"                 );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoFonte.class.php"                   );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoMovimentoContabil.class.php"       );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoPagamentoEmpenho.class.php"        );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoReceitaArrecadada.class.php"       );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoAtualizarPrevisaoReceita.class.php");
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoPrevisaoReceita.class.php"         );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoLiquidacaoEmpenho.class.php"       );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoEmpenho.class.php"                 );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoProjetoAtividade.class.php"        );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoOrgao.class.php"                   );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoUnidadeOrcamentaria.class.php"     );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoPrograma.class.php"                );

class RExportacaoTCERJArquivosPrincipais
{
    /**
        *@var object
        *@acess private
    */
    public $obFExportacaoAlteracaoOrcamentaria;
    public $obFExportacaoPlanoContas;
    public $obFExportacaoDotacaoOrcamentaria;
    public $obFExportacaoItensDespesa;
    public $obFExportacaoItensReceita;
    public $obFExportacaoEstorno;
    public $obFExportacaoFonte;
    public $obFExportacaoMovimentoContabil;
    public $obFExportacaoPagamentoEmpenho;
    public $obFExportacaoReceitaArrecadada;
    public $obFExportacaoAtualizarPrevisaoReceita;
    public $obFExportacaoPrevisaoReceita;
    public $obFExportacaoLiquidacaoEmpenho;
    public $obFExportacaoEmpenho;
    public $obFExportacaoProjetoAtividade;
    public $obFExportacaoOrgao;
    public $obFExportacaoUnidadeOrcamentaria;
    public $obFExportacaoPrograma;
    public $inExercicio      ;
    public $arArquivosSelecionados;
    public $dtDataInicial;
    public $dtDataFinal;
    public $stEntidades;
    public $inPeriodo;

    /**
        *@access public
        *@param object valor
    */
    public function setExercicio($valor) { $this->inExercicio               = $valor;}
    public function setArquivosSelecionados($valor) { $this->arArquivosSelecionados    = $valor;}
    public function setDataInicial($valor) { $this->dtDataInicial             = $valor;}
    public function setDataFinal($valor) { $this->dtDataFianl               = $valor;}
    public function setEntidades($valor) { $this->stEntidades               = $valor;}
    public function setPeriodo($valor) { $this->inPeriodo                 = $valor;}

    /**
     * @access Public
     * @param Object $valor
    */
    public function getExercicio() { return $this->inExercicio;              }
    public function getArquivosSelecionados() { return $this->arArquivosSelecionados;   }
    public function getDataInicial() { return $this->dtDataInicial;            }
    public function getDataFinal() { return $this->dtDataFianl;              }
    public function getEntidades() { return $this->stEntidades;              }
    public function getPeriodo() { return $this->inPeriodo;                }

    /**
        * Método Construtor
        * @access Private
    */
    public function RExportacaoTCERJArquivosPrincipais()
    {
        $this->obFExportacaoAlteracaoOrcamentaria               = new FExportacaoAlteracaoOrcamentaria;
        $this->obFExportacaoPlanoContas                         = new FExportacaoPlanoContas;
        $this->obFExportacaoDotacaoOrcamentaria                 = new FExportacaoDotacaoOrcamentaria;
        $this->obFExportacaoItensDespesa                        = new FExportacaoItensDespesa;
        $this->obFExportacaoItensReceita                        = new FExportacaoItensReceita;
        $this->obFExportacaoEstorno                             = new FExportacaoEstorno;
        $this->obFExportacaoFonte                               = new FExportacaoFonte;
        $this->obFExportacaoMovimentoContabil                   = new FExportacaoMovimentoContabil;
        $this->obFExportacaoPagamentoEmpenho                    = new FExportacaoPagamentoEmpenho;
        $this->obFExportacaoReceitaArrecadada                   = new FExportacaoReceitaArrecadada;
        $this->obFExportacaoAtualizarPrevisaoReceita            = new FExportacaoAtualizarPrevisaoReceita;
        $this->obFExportacaoPrevisaoReceita                     = new FExportacaoPrevisaoReceita;
        $this->obFExportacaoLiquidacaoEmpenho                   = new FExportacaoLiquidacaoEmpenho;
        $this->obFExportacaoEmpenho                             = new FExportacaoEmpenho;
        $this->obFExportacaoProjetoAtividade                    = new FExportacaoProjetoAtividade;
        $this->obFExportacaoOrgao                               = new FExportacaoOrgao;
        $this->obFExportacaoUnidadeOrcamentaria                 = new FExportacaoUnidadeOrcamentaria;
        $this->obFExportacaoPrograma                            = new FExportacaoPrograma;
    }

    /**
    * Método abstrato
    * @access Public
    */
    public function geraRecordSet(&$arRecordSet , $stOrder = "")
    {
        $stOrder            = " ORDER BY exercicio DESC ";
        $arRecordSet        = array('ALTORC'    =>new RecordSet(),
                                    'CONTACONT' =>new RecordSet(),
                                    'DOTACAO'   =>new RecordSet(),
                                    'EMPENHO'   =>new RecordSet(),
                                    'ESPDESP'   =>new RecordSet(),
                                    'ESPREC'    =>new RecordSet(),
                                    'ESTOREMP'  =>new RecordSet(),
                                    'FONTE'     =>new RecordSet(),
                                    'LIQEMP'    =>new RecordSet(),
                                    'MOVCONTA'  =>new RecordSet(),
                                    'PAGEMP'    =>new RecordSet(),
                                    'PREVREC'   =>new RecordSet(),
                                    'RECLANC'   =>new RecordSet(),
                                    'APREVREC'  =>new RecordSet(),
                                    'PROJATV'   =>new RecordSet(),
                                    'ORGAO'     =>new RecordSet(),
                                    'UNIDORCA'  =>new RecordSet(),
                                    'PROGRAMA'  =>new RecordSet());

        if (in_array("ALTORC.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoAlteracaoOrcamentaria->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoAlteracaoOrcamentaria->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoAlteracaoOrcamentaria->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoAlteracaoOrcamentaria->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoAlteracaoOrcamentaria->recuperaDadosExportacao( $arRecordSet['ALTORC'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("CONTACONT.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoPlanoContas->setDado('stExercicio'   , $this->getExercicio()         );
            $obErro = $this->obFExportacaoPlanoContas->recuperaDadosExportacao( $arRecordSet['CONTACONT'], $stFiltro, "cod_estrutural" );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("DOTACAO.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoDotacaoOrcamentaria->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoDotacaoOrcamentaria->setDado('stEntidades'   , $this->getEntidades()         );
            $obErro = $this->obFExportacaoDotacaoOrcamentaria->recuperaDadosExportacao( $arRecordSet['DOTACAO'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("EMPENHO.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoEmpenho->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoEmpenho->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoEmpenho->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoEmpenho->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoEmpenho->recuperaDadosExportacao( $arRecordSet['EMPENHO'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("ESPDESP.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoItensDespesa->setDado('stExercicio'   , $this->getExercicio()    );
            $this->obFExportacaoItensDespesa->setDado('stCodEntidades', $this->getEntidades()    );

            $obErro = $this->obFExportacaoItensDespesa->recuperaDadosExportacao( $arRecordSet['ESPDESP'], $stFiltro, "cod_estrutural" );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("ESPREC.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoItensReceita->setDado('stExercicio'   , $this->getExercicio()    );
            $this->obFExportacaoItensReceita->setDado('stCodEntidades', $this->getEntidades()    );

            $obErro = $this->obFExportacaoItensReceita->recuperaDadosExportacao( $arRecordSet['ESPREC'], $stFiltro, "cod_estrutural" );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("ESTOREMP.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoEstorno->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoEstorno->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoEstorno->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoEstorno->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoEstorno->recuperaDadosExportacao( $arRecordSet['ESTOREMP'], $stFiltro, "exercicio,cod_empenho" );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("FONTE.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoFonte->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoFonte->setDado('stCodEntidades', $this->getEntidades()         );
            $obErro = $this->obFExportacaoFonte->recuperaDadosExportacao( $arRecordSet['FONTE'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("MOVCONTA.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoMovimentoContabil->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoMovimentoContabil->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoMovimentoContabil->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoMovimentoContabil->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoMovimentoContabil->recuperaDadosExportacao( $arRecordSet['MOVCONTA'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("PAGEMP.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoPagamentoEmpenho->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoPagamentoEmpenho->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoPagamentoEmpenho->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoPagamentoEmpenho->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoPagamentoEmpenho->recuperaDadosExportacao( $arRecordSet['PAGEMP'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("RECLANC.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoReceitaArrecadada->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoReceitaArrecadada->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoReceitaArrecadada->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoReceitaArrecadada->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoReceitaArrecadada->recuperaDadosExportacao( $arRecordSet['RECLANC'], $stFiltro, "cod_estrutural" );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("APREVREC.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoAtualizarPrevisaoReceita->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoAtualizarPrevisaoReceita->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoAtualizarPrevisaoReceita->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoAtualizarPrevisaoReceita->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoAtualizarPrevisaoReceita->recuperaDadosExportacao( $arRecordSet['APREVREC'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("PREVREC.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoPrevisaoReceita->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoPrevisaoReceita->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoPrevisaoReceita->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoPrevisaoReceita->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoPrevisaoReceita->recuperaDadosExportacao( $arRecordSet['PREVREC'], $stFiltro, "cod_estrutural" );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("LIQEMP.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoLiquidacaoEmpenho->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoLiquidacaoEmpenho->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoLiquidacaoEmpenho->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoLiquidacaoEmpenho->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoLiquidacaoEmpenho->recuperaDadosExportacao( $arRecordSet['LIQEMP'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("PROJATV.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoProjetoAtividade->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoProjetoAtividade->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoProjetoAtividade->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoProjetoAtividade->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoProjetoAtividade->recuperaDadosExportacao( $arRecordSet['PROJATV'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("ORGAO.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoOrgao->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoOrgao->setDado('stCodEntidades', $this->getEntidades()         );
            $obErro = $this->obFExportacaoOrgao->recuperaDadosExportacao( $arRecordSet['ORGAO'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("UNIDORCA.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoUnidadeOrcamentaria->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoUnidadeOrcamentaria->setDado('stCodEntidades', $this->getEntidades()         );
            $obErro = $this->obFExportacaoUnidadeOrcamentaria->recuperaDadosExportacao( $arRecordSet['UNIDORCA'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
        if (in_array("PROGRAMA.TXT",$this->getArquivosSelecionados())) {
            $this->obFExportacaoPrograma->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoPrograma->setDado('stCodEntidades', $this->getEntidades()         );
            $obErro = $this->obFExportacaoPrograma->recuperaDadosExportacao( $arRecordSet['PROGRAMA'], $stFiltro, $stOrder );
            if ($obErro->ocorreu()) { return $obErro; }
            }
    }

}
