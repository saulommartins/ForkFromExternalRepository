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

 * Classe de mapeamento da tabela tcepe.fonte_recurso
 * Data de Criação   : 30/09/2014

 * @author Analista: Eduardo Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes
 *
 * $Id: TTCEPEElencoContas.class.php 60463 2014-10-23 13:08:10Z carolina $
 * $Date: 2014-10-23 11:08:10 -0200 (Thu, 23 Oct 2014) $
 * $Author: carolina $
 * $Rev: 60463 $
 *
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEPEElencoContas extends Persistente
{
    public function recuperaDadosExportacaoArquivoElencoContas(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        
        $stSQL = $this->montaRecuperaDadosExportacaoArquivoElencoContas($stFiltro, $stOrdem);
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }
    
    private function montaRecuperaDadosExportacaoArquivoElencoContas($stFiltro = '', $stOrdem = '')
    {
        $stSql = "
                SELECT exercicio
                     , estrutural
                     , nom_conta
                     , cod_conta_reduzido
                     , funcao
                     , natureza_saldo
                     , funcionalidade_conta
                     , escriturada
                     , sistema_contabil
                     , tipo_conta_contabil
                     , conta_contabil_superior
                     , nivel                  
                     , COALESCE(tipo_conta_corrente.cod_tipo, 0) AS tipo_conta_corrente
                  FROM tcepe.consulta_arquivo_elenco_contas ( '".$this->getDado('exercicio')."'
                                                            , '".$this->getDado('cod_entidade')."'
                                                            , '".$this->getDado('dt_incial')."'
                                                            , '".$this->getDado('dt_final')."')
                    AS retorno( exercicio CHAR(4)
                              , cod_estrutural VARCHAR
                              , estrutural TEXT
                              , nom_conta VARCHAR
                              , cod_conta_reduzido TEXT
                              , funcao TEXT
                              , natureza_saldo INTEGER
                              , funcionalidade_conta VARCHAR
                              , escriturada TEXT
                              , sistema_contabil INTEGER
                              , tipo_conta_contabil INTEGER
                              , conta_contabil_superior VARCHAR
                              , nivel INTEGER
                              , atributo_tcepe INTEGER
                              , vl_saldo_anterior NUMERIC
                              , vl_saldo_debitos NUMERIC
                              , vl_saldo_creditos NUMERIC
                              , vl_saldo_atual NUMERIC )
               LEFT JOIN tcepe.tipo_conta_corrente
                         ON tipo_conta_corrente.cod_tipo = retorno.atributo_tcepe
        ";
        
        return $stSql;
    }
}

?>