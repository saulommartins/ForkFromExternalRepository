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
     * Classe de mapeamento para a tabela IMOBILIARIO.LOCALIZACAO_VALOR_M2
     * Data de Criação: 21/12/2007

     * @author Analista: Fabio Bertoldi Rodrigues
     * @author Desenvolvedor: Fernando Piccini Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMLocalizacaoValorM2.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.07
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMLocalizacaoValorM2 extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMLocalizacaoValorM2()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.localizacao_valor_m2');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_localizacao,timestamp');

        $this->AddCampo( 'cod_localizacao', 'integer', true, '', true, true );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
        $this->AddCampo( 'cod_norma', 'integer', true, '', false, true );
        $this->AddCampo( 'dt_vigencia', 'date', true, '', false, false );
        $this->AddCampo( 'valor_m2_territorial', 'numeric', true, '14.2', false, false );
        $this->AddCampo( 'valor_m2_predial', 'numeric', true, '14.2', false, false );
    }

    public function listaLocalizacaoValorM2(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaLocalizacaoValorM2($stFiltro);
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaLocalizacaoValorM2($stFiltro)
    {
        $stSQL  = " SELECT
                        to_char( localizacao_valor_m2.dt_vigencia, 'dd/mm/YYYY' ) AS dt_vigencia,
                        localizacao_valor_m2.cod_norma,
                        localizacao_valor_m2.valor_m2_territorial,
                        localizacao_valor_m2.valor_m2_predial,
                        norma.nom_norma

                    FROM
                        imobiliario.localizacao_valor_m2
                    INNER JOIN
                        normas.norma
                    ON
                        norma.cod_norma = localizacao_valor_m2.cod_norma

                    WHERE
                        localizacao_valor_m2.dt_vigencia <= now()
                        ".$stFiltro."
                    ORDER BY
                        localizacao_valor_m2.timestamp
                    DESC LIMIT 1 \n";

        return $stSQL;
    }
}
