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
    * @author Analista: Gelson
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 22/01/2007

  * @author Analista: Gelson
  * @author Desenvolvedor: Carlos Adriano

*/
class TTCMGOEmpenhoModalidade extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOEmpenhoModalidade()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.empenho_modalidade");

        $this->setCampoCod('cod_empenho');
        $this->setComplementoChave('cod_entidade, cod_modalidade, exercicio');

        $this->AddCampo( 'cod_entidade'          , 'integer'    , true  , ''     , true  , true  );
        $this->AddCampo( 'cod_empenho'           , 'integer'    , true  , ''     , true  , true  );
        $this->AddCampo( 'exercicio'             , 'char'       , true  , '4'    , true  , true  );
        $this->AddCampo( 'cod_modalidade'        , 'char'       , true  , '2'    , true  , true  );
        $this->AddCampo( 'cod_fundamentacao'     , 'char'       , false , '2'    , true  , true  );
        $this->AddCampo( 'justificativa'         , 'char'       , false , '250');
        $this->AddCampo( 'razao_escolha'         , 'char'       , false , '245');
    }

    public function recuperaEmpenhoModalidade(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEmpenhoModalidade();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaEmpenhoModalidade()
    {
        $stSql  = " SELECT * FROM tcmgo.empenho_modalidade \n";

        return $stSql;
    }
}
