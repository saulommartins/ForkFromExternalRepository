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
  * Classe de mapeamento da tabela ECONOMICO.ELEMENTO_LICENCA_DIVERSA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMElementoLicencaDiversa.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ELEMENTO_LICENCA_DIVERSA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMElementoLicencaDiversa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMElementoLicencaDiversa()
{
    parent::Persistente();
    $this->setTabela('economico.elemento_licenca_diversa');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_elemento,cod_tipo,cod_licenca,exercicio,ocorrencia');

    $this->AddCampo('cod_elemento','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_licenca','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('ocorrencia','integer',true,'',true,false);

}

function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montarecuperaRelacionamento()
{
    $stSql .= "     select                                                  \n\r";
    $stSql .= "         la.cod_licenca  ,                                   \n\r";
    $stSql .= "         e.cod_elemento  ,                                   \n\r";
    $stSql .= "         el.ocorrencia   ,                                   \n\r";
    $stSql .= "         e.nom_elemento  ,                                   \n\r";
    $stSql .= "         l.exercicio     ,                                   \n\r";
    $stSql .= "         el.cod_tipo     ,                                   \n\r";
    $stSql .= "         el.ocorrencia                                       \n\r";
    $stSql .= "     from                                                    \n\r";
    $stSql .= "         economico.vw_licenca_ativa              as la   ,   \n\r";
    $stSql .= "         economico.licenca_diversa               as l    ,   \n\r";
    $stSql .= "         economico.elemento_licenca_diversa      as el   ,   \n\r";
    $stSql .= "         economico.elemento                      as e        \n\r";
    $stSql .= "     where                                                   \n\r";
    $stSql .= "         l.cod_licenca   = la.cod_licenca    AND             \n\r";
    $stSql .= "         l.exercicio     = la.exercicio      AND             \n\r";
    $stSql .= "         el.cod_licenca  = l.cod_licenca     AND             \n\r";
    $stSql .= "         el.cod_tipo     = l.cod_tipo        AND             \n\r";
    $stSql .= "         el.exercicio    = l.exercicio       AND             \n\r";
    $stSql .= "         el.cod_elemento = e.cod_elemento                    \n\r";

return $stSql;
}

}
