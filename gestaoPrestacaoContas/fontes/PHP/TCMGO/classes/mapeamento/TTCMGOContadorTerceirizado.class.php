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
    * Classe de mapeamento da tabela tcmgo.unidade_responsavel
    * Data de Criação   : 23/12/2008

    * @author Analista      Gelson
    * @author Desenvolvedor Carlos Adriano

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOContadorTerceirizado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TTCMGOContadorTerceirizado()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.contador_terceirizado");

        $this->setCampoCod('numcgm');
        $this->setComplementoChave('num_orgao, num_unidade, exercicio, timestamp');

        $this->AddCampo( 'num_unidade' , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'num_orgao'   , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'exercicio'   , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'numcgm'      , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'timestamp'   , 'timestamp'  , true  , ''   , true  , true);
    }

 function recuperaPorUnidade(&$rsRecordSet)
 {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaPorUnidade();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaPorUnidade()
    {
        $stSql  = "    SELECT *                                                                      \n";
        $stSql .= "      FROM tcmgo.contador_terceirizado                                            \n";

        $stSql .= "INNER JOIN sw_cgm                                                                 \n";
        $stSql .= "        ON sw_cgm.numcgm = contador_terceirizado.numcgm                           \n";

        $stSql .= "     WHERE contador_terceirizado.num_orgao   = ".$this->getDado('num_orgao')."    \n";
        $stSql .= "       AND contador_terceirizado.num_unidade = ".$this->getDado('num_unidade')."  \n";
        $stSql .= "       AND contador_terceirizado.exercicio   = ".$this->getDado('exercicio')."    \n";
        $stSql .= "       AND contador_terceirizado.timestamp   = '".$this->getDado('timestamp')."'  \n";

        $stSql .= "  ORDER BY contador_terceirizado.timestamp DESC LIMIT 1                           \n";

        return $stSql;
    }
}
