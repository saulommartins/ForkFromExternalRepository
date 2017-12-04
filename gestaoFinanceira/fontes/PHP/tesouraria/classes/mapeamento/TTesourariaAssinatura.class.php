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
    * Classe de mapeamento da tabela TESOURARIA_ASSINATURA
    * Data de Criação: 01/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.01
*/

/*
$Log$
Revision 1.11  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_ASSINATURA
  * Data de Criação: 01/09/2005

  * @author Analista: Lucas Oiagen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaAssinatura extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaAssinatura()
{
    parent::Persistente();
    $this->setTabela("tesouraria.assinatura");

    $this->setCampoCod('');
     $this->setComplementoChave('cod_entidade,numcgm,exercicio,tipo');

    $this->AddCampo('cod_entidade'  , 'integer', true, '' , true , true  );
    $this->AddCampo('numcgm'        , 'integer', true, '' , true , true  );
    $this->AddCampo('exercicio'     , 'char'   , true, '4', true , false );
    $this->AddCampo('tipo'          , 'varchar', true, '' , true , false );
    $this->AddCampo('cargo'         , 'varchar', true, '' , false, false );
    $this->AddCampo('num_matricula' , 'varchar', true, '' , false, false );
    $this->AddCampo('situacao'      , 'boolean', true, '' , false, false );

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT TA.numcgm                             \n";
    $stSql .= "       ,CGM.nom_cgm                           \n";
    $stSql .= "       ,TA.exercicio                          \n";
    $stSql .= "       ,TA.cargo                              \n";
    $stSql .= "       ,TA.situacao                           \n";
    $stSql .= "       ,TA.cod_entidade                       \n";
    $stSql .= "       ,TA.tipo                               \n";
    $stSql .= "       ,TA.num_matricula                      \n";
    $stSql .= " FROM tesouraria.assinatura           AS TA,  \n";
    $stSql .= "      sw_cgm                          AS CGM  \n";
    $stSql .= " WHERE TA.numcgm     = CGM.numcgm             \n";

    return $stSql;
}

}
