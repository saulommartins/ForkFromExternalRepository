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
     * Classe de mapeamento para a tabela IMOBILIARIO.TRECHO_ALIQUOTA
     * Data de Criação: 20/12/2007

     * @author Analista: Fabio Bertoldi Rodrigues
     * @author Desenvolvedor: Fernando Piccini Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMTrechoAliquota.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.06
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMTrechoAliquota extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMTrechoAliquota()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.trecho_aliquota');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_logradouro,cod_trecho,timestamp');

        $this->AddCampo( 'cod_logradouro', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_trecho', 'integer', true, '', true, true );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
        $this->AddCampo( 'cod_norma', 'integer', true, '', false, true );
        $this->AddCampo( 'dt_vigencia', 'date', true, '', false, false );
        $this->AddCampo( 'aliquota_territorial', 'numeric', true, '14.2', false, false );
        $this->AddCampo( 'aliquota_predial', 'numeric', true, '14.2', false, false );
    }

    public function listaTrechoAliquota(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaTrechoAliquota($stFiltro);
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaTrechoAliquota($stFiltro)
    {
        $stSQL  = " SELECT
                        to_char( trecho_aliquota.dt_vigencia, 'dd/mm/YYYY' ) AS dt_vigencia,
                        trecho_aliquota.cod_norma,
                        trecho_aliquota.aliquota_territorial,
                        trecho_aliquota.aliquota_predial,
                        norma.nom_norma

                    FROM
                        imobiliario.trecho_aliquota
                    INNER JOIN
                        normas.norma
                    ON
                        norma.cod_norma = trecho_aliquota.cod_norma

                    WHERE
                        trecho_aliquota.dt_vigencia <= now()
                        ".$stFiltro."
                    ORDER BY
                        trecho_aliquota.timestamp
                    DESC LIMIT 1 \n";

        return $stSQL;
    }
}
