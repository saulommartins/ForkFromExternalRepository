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
     * Classe de mapeamento para a tabela IMOBILIARIO.BAIRRO_ALIQUOTA
     * Data de Criação: 20/12/2007

     * @author Analista: Fabio Bertoldi Rodrigues
     * @author Desenvolvedor: Fernando Piccini Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMBairroAliquota.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.05
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMBairroAliquota extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMBairroAliquota()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.bairro_aliquota');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_bairro,cod_municipio,cod_uf,timestamp');

        $this->AddCampo( 'cod_bairro', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_municipio', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_uf', 'integer', true, '', true, true );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
        $this->AddCampo( 'cod_norma', 'integer', true, '', false, true );
        $this->AddCampo( 'dt_vigencia', 'date', true, '', false, false );
        $this->AddCampo( 'aliquota_territorial', 'numeric', true, '14.2', false, false );
        $this->AddCampo( 'aliquota_predial', 'numeric', true, '14.2', false, false );
    }

    public function listaBairroAliquota(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaBairroAliquota($stFiltro);
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaBairroAliquota($stFiltro)
    {
        $stSQL  = " SELECT
                        to_char( bairro_aliquota.dt_vigencia, 'dd/mm/YYYY' ) AS dt_vigencia,
                        bairro_aliquota.cod_norma,
                        bairro_aliquota.aliquota_territorial,
                        bairro_aliquota.aliquota_predial,
                        norma.nom_norma

                    FROM
                        imobiliario.bairro_aliquota
                    INNER JOIN
                        normas.norma
                    ON
                        norma.cod_norma = bairro_aliquota.cod_norma

                    WHERE
                        bairro_aliquota.dt_vigencia <= now()
                        ".$stFiltro."
                    ORDER BY
                        bairro_aliquota.timestamp
                    DESC LIMIT 1 \n";

        return $stSQL;
    }
}
