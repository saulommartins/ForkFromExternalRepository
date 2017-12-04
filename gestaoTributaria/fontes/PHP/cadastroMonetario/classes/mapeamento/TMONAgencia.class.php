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
    * Classe de regra de negócio para MONETARIO.AGENCIA
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONAgencia.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONAgencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONAgencia()
{
    parent::Persistente();
    $this->setTabela('monetario.agencia');

    $this->setCampoCod('cod_agencia');
    $this->setComplementoChave('cod_banco');

    $this->AddCampo('cod_banco','integer',true,'',true,true);
    $this->AddCampo('cod_agencia','integer',true,'',true,false);
    $this->AddCampo('num_agencia','varchar',true,'',false,false);
    $this->AddCampo('nom_agencia','varchar',true,'',false,false);
    $this->AddCampo('numcgm_agencia','integer',true,'',false,true);
    $this->AddCampo('nom_pessoa_contato','varchar',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
 $stSql  = "SELECT                                             \n";
 $stSql .= "    Ag.*,                                          \n";
 $stSql .= "    Ban.*                                          \n";
 $stSql .= "FROM                                               \n";
 $stSql .= "    monetario.agencia AS ag                        \n";
 $stSql .= "INNER JOIN                                         \n";
 $stSql .= "    monetario.banco AS ban                         \n";
 $stSql .= "ON                                                 \n";
 $stSql .= "    ag.cod_banco = ban.cod_banco                   \n";

 return $stSql;

}
}
