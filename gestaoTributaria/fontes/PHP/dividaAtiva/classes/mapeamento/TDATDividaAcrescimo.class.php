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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_ACRESCIMO
    * Data de Criação: 02/07/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: André Machado
    * @package URBEM
    * @subpackage Mapeamento

* Casos de uso: uc-05.04.02
*/

/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaAcrescimo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaAcrescimo()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_acrescimo');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento, num_parcela');

        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'4',true,false);

        $this->AddCampo('cod_acrescimo','integer',false,'',false,false);
        $this->AddCampo('cod_tipo','integer',false,'',false,false);
        $this->AddCampo('valor','numeric',false,'',false,false);
    }

    public function lancarAcrescimos($stExercicio, $inCodInscricao, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = " SELECT divida.fn_acrescimo_divida_individual( ".$stExercicio."::varchar,".$inCodInscricao.")";
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
}

?>
