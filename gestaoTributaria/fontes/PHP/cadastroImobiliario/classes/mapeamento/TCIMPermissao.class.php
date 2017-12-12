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
     * Classe de mapeamento para a tabela IMOBILIARIO.PERMISSAO
     * Data de Criação: 17/03/2008

     * @author Analista: Fabio Bertoldi
     * @author Desenvolvedor: Fernando Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMPermissao.class.php 65128 2016-04-26 20:07:17Z evandro $

     * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMPermissao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.permissao');

        $this->setCampoCod( '' );
        $this->setComplementoChave( 'cod_tipo,numcgm,timestamp' );

        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, true );
        $this->AddCampo( 'numcgm', 'integer', true, '', true, true );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
    }

    public function recuperaListaPermissoes(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaPermissoes();
        $this->setDebug( $stSql );
        //$this->debug(); exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaPermissoes()
    {
        $stSql = "
            SELECT
                permissao.numcgm AS inCGM,
                sw_cgm.nom_cgm AS stNomCGM,
                permissao.cod_tipo AS inTipoLicenca,
                tipo_licenca.nom_tipo

            FROM
                imobiliario.permissao

            INNER JOIN
                sw_cgm
            ON
                sw_cgm.numcgm = permissao.numcgm

            INNER JOIN
                imobiliario.tipo_licenca
            ON
                tipo_licenca.cod_tipo = permissao.cod_tipo

            WHERE
                permissao.timestamp = (
                    SELECT
                        timestamp
                    FROM
                        imobiliario.permissao
                    ORDER BY
                        timestamp DESC
                    LIMIT 1
                )
        ";

        return $stSql;
    }
}
