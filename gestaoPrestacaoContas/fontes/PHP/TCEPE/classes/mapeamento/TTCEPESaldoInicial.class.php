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
 * Classe de mapeamento da tabela tcepe.fonte_recurso
 * Data de Criação   : 30/09/2014

 * @author Analista: Eduardo Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes
 *
 * $Id: TTCEPESaldoInicial.class.php 60579 2014-10-31 12:56:40Z michel $
 * $Date: 2014-10-31 10:56:40 -0200 (Fri, 31 Oct 2014) $
 * $Author: michel $
 * $Rev: 60579 $
 *
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEPESaldoInicial extends Persistente
{
    public function recuperaDadosExportacaoArquivoSaldoInicial(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        
        $stSQL = $this->montaRecuperaDadosExportacaoArquivoSaldoInicial($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        
        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }
    
    private function montaRecuperaDadosExportacaoArquivoSaldoInicial($stFiltro = '', $stOrdem = '')
    {
        $stSql = "
                SELECT  lpad(regexp_replace(cc.num_conta_corrente,'[.|-]','','gi'),12,'0') AS num_conta_bancaria
                        , COALESCE(SUM(vl.vl_lancamento),'0.00') AS vl_saldo_conciliado
                        , plano_banco_tipo_conta_banco.cod_tipo_conta_banco AS tipo_conta_bancaria
                FROM contabilidade.plano_conta AS pc
                JOIN contabilidade.plano_analitica AS pa
                     ON pa.exercicio = pc.exercicio
                    AND pa.cod_conta = pc.cod_conta
                JOIN contabilidade.plano_banco AS pb
                     ON pb.exercicio = pa.exercicio
                    AND pb.cod_plano = pa.cod_plano
                JOIN monetario.conta_corrente as cc
                     ON cc.cod_banco = pb.cod_banco
                    AND cc.cod_agencia = pb.cod_agencia
                    AND cc.cod_conta_corrente = pb.cod_conta_corrente
                LEFT JOIN tcepe.plano_banco_tipo_conta_banco
                     ON plano_banco_tipo_conta_banco.exercicio = pb.exercicio
                    AND plano_banco_tipo_conta_banco.cod_plano = pb.cod_plano
                LEFT JOIN ( SELECT * FROM contabilidade.conta_debito WHERE exercicio = '".$this->getDado('exercicio')."' and tipo = 'I'
                            UNION
                            SELECT * FROM contabilidade.conta_credito WHERE exercicio = '".$this->getDado('exercicio')."' and tipo = 'I'
                ) AS debito_credito
                     ON debito_credito.exercicio = pa.exercicio
                    AND debito_credito.cod_plano = pa.cod_plano
                LEFT JOIN contabilidade.valor_lancamento AS vl
                     ON debito_credito.cod_lote     = vl.cod_lote
                    AND debito_credito.tipo         = vl.tipo
                    AND debito_credito.sequencia    = vl.sequencia
                    AND debito_credito.exercicio    = vl.exercicio
                    AND debito_credito.tipo_valor   = vl.tipo_valor
                    AND debito_credito.cod_entidade = vl.cod_entidade
                LEFT JOIN contabilidade.lancamento AS la
                     ON vl.cod_lote     = la.cod_lote
                    AND vl.tipo         = la.tipo
                    AND vl.sequencia    = la.sequencia
                    AND vl.exercicio    = la.exercicio
                    AND vl.cod_entidade = la.cod_entidade
                LEFT JOIN contabilidade.lote AS lo
                     ON la.cod_lote     = lo.cod_lote
                    AND la.exercicio    = lo.exercicio
                    AND la.tipo         = lo.tipo
                    AND la.cod_entidade = lo.cod_entidade                
                WHERE pb.exercicio = '".$this->getDado('exercicio')."'                
                AND pb.cod_entidade IN('".$this->getDado('cod_entidade')."')
                GROUP BY cc.num_conta_corrente
                    , plano_banco_tipo_conta_banco.cod_tipo_conta_banco
                    , pc.cod_estrutural
                ORDER BY pc.cod_estrutural
        ";
        
        return $stSql;
    }
}

?>