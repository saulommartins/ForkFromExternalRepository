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
    * Classe de mapeamento da tabela tcmgo.nota_fiscal_empenho
    * Data de Criação   : 23/09/2008

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGONotaFiscalEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGONotaFiscalEmpenho()
{
    parent::Persistente();
    $this->setTabela("tcmgo.nota_fiscal_empenho");

    $this->setCampoCod('cod_nota');
    $this->setComplementoChave('');

    $this->AddCampo( 'cod_nota'           , 'integer' , true  , ''     , true  , true   );
    $this->AddCampo( 'exercicio'          , 'char'    , true  , '4'    , true  , true   );
    $this->AddCampo( 'cod_entidade'       , 'integer' , true  , ''     , true  , true   );
    $this->AddCampo( 'cod_empenho'        , 'integer' , true  , ''     , true  , true   );
    $this->AddCampo( 'vl_associado'       , 'numeric' , true  , '14,2' , false , false  );

}

}

?>
