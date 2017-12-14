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
    * Classe de mapeamento da tabela
    * Data de Criação: 12/07/2016

    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGPlanoContas.class.php 66067 2016-07-14 17:27:32Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMGPlanoContas extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcemg.plano_contas');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_conta,exercicio,cod_uf,cod_plano,codigo_estrutural');

        $this->AddCampo('cod_conta'         ,'integer',true,''  ,true,true);
        $this->AddCampo('exercicio'         ,'char'   ,true,'4' ,true,true);
        $this->AddCampo('cod_uf'            ,'integer',true,''  ,true,true);
        $this->AddCampo('cod_plano'         ,'integer',true,''  ,true,true);
        $this->AddCampo('codigo_estrutural' ,'varchar',true,'30',true,true);
    }

    function montaRecuperaTodos()
    {
        $stSql = "
               SELECT tabela.cod_estrutural
                    , pc.cod_conta
                    , pc.nom_conta
                    , pc.exercicio
                    , pa.cod_plano
                    , pct.cod_uf
                    , pct.cod_plano AS cod_plano_estrutura
                    , CASE WHEN pct.codigo_estrutural IS NULL
                           THEN pce.codigo_estrutural
                           ELSE pct.codigo_estrutural
                      END AS cod_estrutural_estrutura
                    , CASE WHEN pct.codigo_estrutural IS NULL
                           THEN 'Não'
                           ELSE ''
                      END AS vinculado
                 FROM contabilidade.fn_rl_balancete_verificacao( '".$this->getDado('exercicio')."'
                                                               , 'cod_entidade IN (".$this->getDado('cod_entidade').") AND cod_estrutural LIKE ''".$this->getDado('cod_grupo').".%'' AND exercicio = ''".$this->getDado('exercicio')."'''
                                                               , '01/01/".$this->getDado('exercicio')."'
                                                               , '31/12/".$this->getDado('exercicio')."'
                                                               , 'A'
                                                               ) AS tabela
                                                               ( cod_estrutural VARCHAR
                                                               , nivel INTEGER
                                                               , nom_conta VARCHAR
                                                               , cod_sistema INTEGER
                                                               , indicador_superavit CHAR(12)
                                                               , vl_saldo_anterior NUMERIC
                                                               , vl_saldo_debitos NUMERIC
                                                               , vl_saldo_creditos NUMERIC
                                                               , vl_saldo_atual NUMERIC
                                                               )

           INNER JOIN contabilidade.plano_conta AS pc 
                   ON pc.cod_estrutural = tabela.cod_estrutural
                  AND pc.exercicio = '".$this->getDado('exercicio')."'

           INNER JOIN contabilidade.plano_analitica as pa
                   ON pa.cod_conta = pc.cod_conta
                  AND pa.exercicio = pc.exercicio

            LEFT JOIN tcemg.plano_contas AS pct
                   ON pct.cod_conta = pc.cod_conta
                  AND pct.exercicio = pc.exercicio

            LEFT JOIN (
                       select publico.fn_mascarareduzida(plano_conta_estrutura.codigo_estrutural)||'%' AS estrutural_teste
                            , plano_conta_estrutura.codigo_estrutural
                         from contabilidade.plano_conta_estrutura
                        where plano_conta_estrutura.cod_uf = ".$this->getDado('cod_uf')."
                          and plano_conta_estrutura.cod_plano = ".$this->getDado('cod_plano')."
                          and plano_conta_estrutura.escrituracao = 'S' --CONTAS UTILIZADAS PELO TRIBUNAL DE CONTAS DO ESTADO DE MINAS GERAIS
                          and plano_conta_estrutura.codigo_estrutural like '".$this->getDado('cod_grupo').".%'
                          and ( SELECT COUNT(plano_contas.cod_conta)
                                  FROM contabilidade.plano_conta
                            INNER JOIN tcemg.plano_contas
                                    ON plano_contas.cod_conta = plano_conta.cod_conta
                                   AND plano_contas.exercicio = plano_conta.exercicio
                                   AND plano_contas.cod_uf    = plano_conta_estrutura.cod_uf
                                   AND plano_contas.cod_plano = plano_conta_estrutura.cod_plano
                                 WHERE plano_conta.cod_estrutural LIKE '".$this->getDado('cod_grupo').".%'
                                   AND plano_conta.exercicio = '".$this->getDado('exercicio')."'
                              ) = 0
                      ) as pce
                   ON pc.cod_estrutural ILIKE pce.estrutural_teste

                WHERE pc.cod_estrutural LIKE '".$this->getDado('cod_grupo').".%'
                ";

        return $stSql;
    }
    
    function recuperaPlanoContaEstrutura(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(empty($stOrdem))
            $stOrdem = "ORDER BY codigo_estrutural";
        $stSql = $this->montaRecuperaPlanoContaEstrutura().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    function montaRecuperaPlanoContaEstrutura()
    {
        $stSql  = "
               SELECT *
                 FROM contabilidade.plano_conta_estrutura
                WHERE cod_uf = ".$this->getDado('cod_uf')."
                  AND cod_plano = ".$this->getDado('cod_plano')."
                  AND escrituracao = 'S' --CONTAS UTILIZADAS PELO TRIBUNAL DE CONTAS DO ESTADO DE MINAS GERAIS
        ";

        return $stSql;
    }

}
