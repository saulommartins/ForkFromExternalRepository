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
 * Mapeamento da funcao contabilidade.fn_verifica_vinculo_restos
 * Data de Criação   : 07/01/2009

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Henrique Boaventura

 * @package URBEM

 *  $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeVerificaVinculoRestos extends Persistente
{

    public function FContabilidadeVerificaVinculoRestos()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.fn_verifica_vinculo_restos');

        $this->AddCampo('exercicio'             ,'varchar',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql = "
            SELECT *
              FROM " . $this->getTabela() . "('" . $this->getDado('exercicio') . "'";

        if ($this->getDado('cod_entidade') != '' && $this->getDado('cod_entidade') != NULL) {
            $stSql .= ", ".$this->getDado('cod_entidade')."";
        }

        $stSql .= ")
                AS retorno (  cod_entidade integer
                            , exercicio varchar
                            , cod_plano_debito varchar
                            , cod_estrutural varchar )
        ";

        return $stSql;
    }

}
