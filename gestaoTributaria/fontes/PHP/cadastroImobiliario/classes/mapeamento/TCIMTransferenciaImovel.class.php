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
     * Classe de mapeamento para a tabela IMOBILIARIO.TRANSFERENCIA_IMOVEL
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMTransferenciaImovel.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.7  2007/02/09 15:35:35  cercato
correcao da transferencia da baixa automatica.

Revision 1.6  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.TRANSFERENCIA_IMOVEL
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMTransferenciaImovel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMTransferenciaImovel()
{
    parent::Persistente();
    $this->setTabela('imobiliario.transferencia_imovel');

    $this->setCampoCod('cod_transferencia');
    $this->setComplementoChave('');

    $this->AddCampo('cod_transferencia','integer',true,'',true,false);
    $this->AddCampo('cod_natureza','integer',true,'',false,true);
    $this->AddCampo('inscricao_municipal','integer',true,'',false,true);
    $this->AddCampo('dt_cadastro','timestamp',false,'',false,false);

}
function recuperaTransferenciaImovel(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTransferenciaImovel().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaTransferenciaImovel()
{
    $stSql  = "SELECT                                                                  \n";
    $stSql .= "    DISTINCT ON(TI.COD_TRANSFERENCIA)                                   \n";
    $stSql .= "       TI.COD_TRANSFERENCIA  ,                                          \n";
    $stSql .= "       TI.COD_NATUREZA       ,                                          \n";
    $stSql .= "       TA.NUMCGM             ,                                          \n";
    $stSql .= "       TE.observacao         ,                                          \n";
    $stSql .= "       NT.DESCRICAO          ,                                          \n";
    $stSql .= "       TI.INSCRICAO_MUNICIPAL,                                          \n";
    $stSql .= "       TO_CHAR(TI.DT_CADASTRO, 'DD/MM/YYYY') AS DT_CADASTRO,            \n";
    $stSql .= "       MA.MAT_REGISTRO_IMOVEL,                                          \n";
    $stSql .= "       TO_CHAR(TE.DT_EFETIVACAO  , 'DD/MM/YYYY') AS DT_EFETIVACAO,      \n";
    $stSql .= "       TO_CHAR(TC.DT_CANCELAMENTO, 'DD/MM/YYYY') AS DT_CANCELAMENTO,    \n";
    $stSql .= "       TP.COD_PROCESSO       ,                                          \n";
    $stSql .= "       TP.EXERCICIO AS EXERCICIO_PROC ,                                 \n";
    $stSql .= "       TCR.CRECI             ,                                          \n";
    $stSql .= "       CASE                                                             \n";
    $stSql .= "            WHEN                                                        \n";
    $stSql .= "                IM.CRECI IS NOT NULL                                    \n";
    $stSql .= "         THEN                                                           \n";
    $stSql .= "                IM.NUMCGM                                               \n";
    $stSql .= "            WHEN                                                        \n";
    $stSql .= "                COR.CRECI IS NOT NULL                                   \n";
    $stSql .= "            THEN                                                        \n";
    $stSql .= "                COR.NUMCGM                                              \n";
    $stSql .= "       END AS NUMCGM_CRECI         ,                                    \n";
    $stSql .= "       CGM.NOM_CGM           ,                                          \n";
    $stSql .= "       publico.fn_mascara_dinamica(TRIM(CO.VALOR), CAST((TI.INSCRICAO_MUNICIPAL) AS VARCHAR)) AS INSCRICAO_MASCARA  \n";
    $stSql .= "   FROM                                                                 \n";
    $stSql .= "       (                                                                \n";
    $stSql .= "       SELECT                                                           \n";
    $stSql .= "           TI.*                                                         \n";
    $stSql .= "       FROM                                                             \n";
    $stSql .= "         imobiliario.transferencia_imovel AS TI,                            \n";
    $stSql .= "           (                                                            \n";
    $stSql .= "           SELECT                                                       \n";
    $stSql .= "               DT_CADASTRO,                                             \n";
    $stSql .= "               INSCRICAO_MUNICIPAL                                      \n";
    $stSql .= "           FROM                                                         \n";
    $stSql .= "             imobiliario.transferencia_imovel                               \n";
    $stSql .= "           GROUP BY                                                     \n";
    $stSql .= "               INSCRICAO_MUNICIPAL,DT_CADASTRO                          \n";
    $stSql .= "           ) AS TTI                                                     \n";
    $stSql .= "       WHERE                                                            \n";
    $stSql .= "           TI.INSCRICAO_MUNICIPAL = TTI.INSCRICAO_MUNICIPAL             \n";
    $stSql .= "           AND TI.DT_CADASTRO     = TTI.DT_CADASTRO                     \n";
    $stSql .= "       ) AS TI                                                          \n";
    $stSql .= "      INNER JOIN imobiliario.natureza_transferencia AS NT                   \n";
    $stSql .= "         ON TI.COD_NATUREZA = NT.COD_NATUREZA                           \n";
    $stSql .= "      INNER JOIN imobiliario.transferencia_adquirente AS TA                 \n";
    $stSql .= "         ON TI.COD_TRANSFERENCIA = TA.COD_TRANSFERENCIA                 \n";
    $stSql .= "      LEFT JOIN imobiliario.transferencia_efetivacao AS TE                  \n";
    $stSql .= "         ON TI.COD_TRANSFERENCIA = TE.COD_TRANSFERENCIA                 \n";
    $stSql .= "      LEFT JOIN imobiliario.transferencia_cancelamento AS TC                \n";
    $stSql .= "         ON TI.COD_TRANSFERENCIA = TC.COD_TRANSFERENCIA                 \n";
    $stSql .= "      LEFT JOIN imobiliario.transferencia_processo AS TP                    \n";
    $stSql .= "         ON TI.COD_TRANSFERENCIA = TP.COD_TRANSFERENCIA                 \n";
    $stSql .= "      LEFT JOIN imobiliario.transferencia_corretagem AS TCR                 \n";
    $stSql .= "         ON TI.COD_TRANSFERENCIA = TCR.COD_TRANSFERENCIA                \n";
    $stSql .= "      LEFT JOIN imobiliario.matricula_imovel AS MA                          \n";
    $stSql .= "         ON TI.INSCRICAO_MUNICIPAL = MA.INSCRICAO_MUNICIPAL             \n";
    $stSql .= "      LEFT JOIN imobiliario.imobiliaria AS IM                               \n";
    $stSql .= "         ON IM.CRECI = TCR.CRECI                                        \n";
    $stSql .= "      LEFT JOIN imobiliario.corretor AS COR                                 \n";
    $stSql .= "         ON COR.CRECI = TCR.CRECI                                       \n";
    $stSql .= "      LEFT JOIN sw_cgm AS CGM                                       \n";
    $stSql .= "         ON IM.NUMCGM  = CGM.NUMCGM OR                                  \n";
    $stSql .= "            COR.NUMCGM = CGM.NUMCGM                                     \n";
    $stSql .= "      INNER JOIN administracao.configuracao AS CO                                 \n";
    $stSql .= "         ON COD_MODULO = 12 AND PARAMETRO = 'mascara_inscricao'         \n";

    return $stSql;
}

function recuperaPagamentoImovelITBI(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montarecuperaPagamentoImovelITBI().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}
function montarecuperaPagamentoImovelITBI()
{
    $stSql  = "SELECT                                                    \n";
    $stSql .="  IC.inscricao_municipal,                          \n";
    $stSql .="  PC.cod_calculo,                                     \n";
    $stSql .="  PC.numeracao,                                     \n";
    $stSql .="  PC.valor as valor_calculo,                      \n";
    $stSql .="  PAG.valor as valor_pago,                       \n";
    $stSql .="  PAG.data_pagamento                            \n";
    $stSql .="FROM                                                       \n";
    $stSql .="  arrecadacao.imovel_calculo as IC          \n";

    $stSql .="INNER JOIN                                                \n";
    $stSql .="  arrecadacao.pagamento_calculo as PC \n";
    $stSql .="ON                                                            \n";
    $stSql .="  PC.cod_calculo = IC.cod_calculo             \n";

    $stSql .="INNER JOIN                                                \n";
    $stSql .="  arrecadacao.calculo as CALC                 \n";
    $stSql .="ON                                                            \n";
    $stSql .="  CALC.cod_calculo = PC.cod_calculo        \n";

    $stSql .="INNER JOIN                                                \n";
    $stSql .="  arrecadacao.pagamento PAG                \n";
    $stSql .="ON                                                            \n";
    $stSql .="  PAG.numeracao = PC.numeracao          \n";

    return $stSql;
}
}
