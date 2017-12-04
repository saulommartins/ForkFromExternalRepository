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
/*
 * Classe de mapeamento da tabela tceam.vinculo_arquivo_licitacao
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMConfiguracaoArquivoLicitacao extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEAMConfiguracaoArquivoLicitacao()
    {
        parent::Persistente();
        $this->setTabela('tceam.configuracao_arquivo_licitacao');

        $this->setComplementoChave('cod_mapa, exercicio');

        $this->AddCampo('cod_mapa'                  , 'integer', true , ''  , true , true);
        $this->AddCampo('exercicio'                 , 'varchar', true , '4' , true , true);
        $this->AddCampo('diario_oficial'            , 'integer', false, '6' , false, false);
        $this->AddCampo('dt_publicacao_homologacao' , 'date'   , false, ''  , false, false, '', '');
    }

    public function recuperaConfiguracaoArquivoLicitacao(&$rsRecordSet, $stCondicao = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConfiguracaoArquivoLicitacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaConfiguracaoArquivoLicitacao()
    {
        $stSql = "
           SELECT mapa.cod_mapa
                , mapa.exercicio
                , CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN
                            'Licitação: ' || licitacao.cod_licitacao || '/' || licitacao.exercicio
                       ELSE
                            'Compra Direta:' || compra_direta.cod_compra_direta || '/' || compra_direta.exercicio
                  END AS licitacao_compra_direta
                  , CASE WHEN licitacao.cod_licitacao IS NOT NULL THEN
                            licitacao.descricao
                       ELSE
                            compra_direta.descricao
                  END AS modalidade
                , configuracao_arquivo_licitacao.diario_oficial
                , to_char(configuracao_arquivo_licitacao.dt_publicacao_homologacao, 'dd/mm/yyyy') AS dt_publicacao_homologacao
             FROM compras.mapa
        LEFT JOIN tceam.configuracao_arquivo_licitacao
               ON configuracao_arquivo_licitacao.cod_mapa  = mapa.cod_mapa
              AND configuracao_arquivo_licitacao.exercicio = mapa.exercicio
        LEFT JOIN ( SELECT licitacao.cod_licitacao
                         , licitacao.cod_mapa
                         , licitacao.exercicio_mapa
                         , licitacao.exercicio
                         , licitacao.timestamp as timestamp
                         , licitacao.cod_modalidade
                         , modalidade.descricao
                      FROM licitacao.licitacao
                      JOIN compras.modalidade
                        ON modalidade.cod_modalidade = licitacao.cod_modalidade
                     WHERE licitacao.cod_entidade IN ( ".$this->getDado('cod_entidade')." )
                ) AS licitacao
               ON licitacao.cod_mapa       = mapa.cod_mapa
              AND licitacao.exercicio_mapa = mapa.exercicio
        LEFT JOIN ( SELECT compra_direta.cod_compra_direta
                         , compra_direta.cod_mapa
                         , compra_direta.exercicio_mapa
                         , compra_direta.exercicio_entidade AS exercicio
                         , compra_direta.timestamp as timestamp
                         , compra_direta.cod_modalidade
                         , modalidade.descricao
                      FROM compras.compra_direta
                      JOIN compras.modalidade
                        ON modalidade.cod_modalidade = compra_direta.cod_modalidade
                     WHERE compra_direta.cod_entidade IN ( ".$this->getDado('cod_entidade')." )
                ) AS compra_direta
               ON compra_direta.cod_mapa       = mapa.cod_mapa
              AND compra_direta.exercicio_mapa = mapa.exercicio
            WHERE mapa.exercicio = '".$this->getDado('exercicio')."'
              AND ((to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."') OR (to_char(compra_direta.timestamp,'mm') = '".$this->getDado('mes')."'))
              AND (compra_direta.cod_compra_direta IS NOT NULL OR licitacao.cod_licitacao IS NOT NULL)
    ";

        return $stSql;
    }

}
