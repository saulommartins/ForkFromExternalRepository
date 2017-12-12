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
    * Classe de mapeamento da tabela ORCAMENTO.RECURSO_DIRETO
    * Data de Criação: 29/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: TOrcamentoRecursoDireto.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.05, uc-02.08.02, uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoRecursoDireto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoRecursoDireto()
{
    parent::Persistente();
    $this->setTabela('orcamento.recurso_direto');

    $this->setCampoCod('cod_recurso');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'04',false,false);
    $this->AddCampo('cod_recurso','integer',true,'',true,false);
    $this->AddCampo('nom_recurso','varchar',true,'80',false,false);
    $this->AddCampo('finalidade','varchar',true,'160',false,false);
    $this->AddCampo('tipo','varchar',true,'1',false,false);
    $this->AddCampo('cod_fonte', 'integer', true, ''  , true  , true );
    $this->AddCampo('codigo_tc', 'integer', false, '' , false , false);
    $this->AddCampo('cod_tipo_esfera', 'integer', false, '' , false , false);

}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                               ".$stQuebra;
    $stSql .= "     R.*,                                             ".$stQuebra;
    $stSql .= "     CASE WHEN R.tipo = 'V' THEN 'Vinculado' ELSE 'Livre' END as nom_tipo, ".$stQuebra;
    $stSql .= "     sw_fn_mascara_dinamica((SELECT valor from administracao.configuracao where cod_modulo=8 and parametro='masc_recurso' and exercicio='".$this->getDado("exercicio")."'),''||R.cod_recurso) as masc_recurso ".$stQuebra;
    $stSql .= " FROM                                                 ".$stQuebra;
    $stSql .= "     orcamento.recurso_direto        AS R                ".$stQuebra;

    return $stSql;
}

}
