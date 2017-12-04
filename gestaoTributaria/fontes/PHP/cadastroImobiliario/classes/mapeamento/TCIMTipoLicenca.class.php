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
     * Classe de mapeamento para a tabela IMOBILIARIO.TIPO_LICENCA
     * Data de Criação: 17/03/2008

     * @author Analista: Fabio Bertoldi
     * @author Desenvolvedor: Fernando Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMTipoLicenca.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMTipoLicenca extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMTipoLicenca()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.tipo_licenca');

        $this->setCampoCod( 'cod_tipo' );
        $this->setComplementoChave( '' );

        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, false );
        $this->AddCampo( 'nom_tipo', 'varchar', true, '80', false, false );
    }

    public function recuperaLicencaPorCGM(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaLicencaPorCGM().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLicencaPorCGM()
    {
        ;
        //-------------
        $stSQL = " SELECT
                        tipo_licenca.cod_tipo,
                        tipo_licenca.nom_tipo,
                        permissao.timestamp

                    FROM
                        imobiliario.tipo_licenca

                    INNER JOIN
                        imobiliario.permissao
                    ON
                        permissao.cod_tipo = tipo_licenca.cod_tipo

                    WHERE
                        permissao.numcgm = ".Sessao::read('numCgm')."
                        AND permissao.timestamp = (
                            SELECT
                                max(timestamp)
                            FROM
                                imobiliario.permissao
                            WHERE
                                tipo_licenca.cod_tipo = permissao.cod_tipo
                        )  ";

        return $stSQL;
    }

}
