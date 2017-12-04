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
 * Classe de mapeamento da tabela tcepe.documento
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id: TTCEPEDocumento.class.php 60198 2014-10-06 19:37:03Z michel $
 * @author Jean Silva
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEDocumento extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author Jean Silva
     */
    public function TTCEPEDocumento()
    {
        parent::Persistente();
        $this->setTabela('tcepe.documento');

        $this->setCampoCod('cod_nota');

        $this->AddCampo('cod_tipo'       , 'integer', true  , ''   , false, true);
        $this->AddCampo('exercicio'      , 'varchar', true  , '4'  , false, true);
        $this->AddCampo('cod_entidade'   , 'integer', true  , ''   , false, true);
        $this->AddCampo('cod_nota'       , 'integer', true  , ''   , false, true);
        $this->AddCampo('nro_documento'  , 'varchar', false , '15' , false, false);
        $this->AddCampo('serie'          , 'integer', false , ''   , false, false);
        $this->AddCampo('cod_uf'         , 'integer', false , ''   , false, false);
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDocumento.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaDocumento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDocumento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDocumento()
    {
        $stSql  =" SELECT documento.*                                   \n";
        $stSql .="      , sw_uf.sigla_uf                                \n";
        $stSql .="      , tipo_documento.descricao AS descricao_tipo    \n";
        $stSql .="   FROM tcepe.documento                               \n";
        $stSql .="   JOIN sw_uf                                         \n";
        $stSql .="     ON sw_uf.cod_uf=documento.cod_uf                 \n";
        $stSql .="     JOIN tcepe.tipo_documento                        \n";
        $stSql .="     ON tipo_documento.cod_tipo=documento.cod_tipo    \n";

        return $stSql;
    }

}
