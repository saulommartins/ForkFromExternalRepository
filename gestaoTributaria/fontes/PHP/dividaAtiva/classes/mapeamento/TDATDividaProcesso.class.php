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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_PROCESSO
    * Data de Criação: 13/04/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaProcesso.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2007/04/13 21:38:16  dibueno
Bug #9032#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATDividaProcesso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDATDividaProcesso()
{
    parent::Persistente();
    $this->setTabela("divida.divida_processo");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_inscricao, exercicio, cod_processo, ano_exercicio');
                  //nome',           tipo         requerido   Tamanho   PK     FK    Conteudo = ''
    $this->AddCampo('cod_inscricao' , 'varchar' , true  ,'17'   ,true,  true );
    $this->AddCampo('exercicio'     , 'varchar' , true  ,'4'    ,true,  true );
    $this->AddCampo('cod_processo'  , 'integer' , true  ,''     ,true,  true );
    $this->AddCampo('ano_exercicio' , 'varchar' , true  ,'4'    ,true,  true );

}

}
?>
