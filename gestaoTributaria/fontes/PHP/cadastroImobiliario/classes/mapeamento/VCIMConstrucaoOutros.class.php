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
     * Classe de mapeamento para a tabela IMOBILIARIO.CONSTRUCAO_OUTROS
     * Data de Criação: 24/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: VCIMConstrucaoOutros.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.7  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CONSTRUCAO_OUTROS
  * Data de Criação: 24/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class VCIMConstrucaoOutros extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VCIMConstrucaoOutros()
{
    parent::Persistente();
    $this->setTabela('imobiliario.vw_construcao_outros');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_construcao' );

    $this->AddCampo('cod_construcao','integer'  );
    $this->AddCampo('descricao'     ,'varchar'  );
    $this->AddCampo('area_real'     ,'numeric'  );
    $this->AddCampo('cod_processo'  ,'integer'  );
    $this->AddCampo('exercicio'     ,'character');
    $this->AddCampo('imovel_cond'   ,'varchar'  );
    $this->AddCampo('nom_condominio','varchar'  );
    $this->AddCampo('tipo_vinculo'  ,'varchar'  );
    $this->AddCampo('data_construcao','date'    );
    $this->AddCampo('timestamp'     ,'timestamp');
    $this->AddCampo('data_baixa'    , 'varchar' );
    $this->AddCampo('situacao'      , 'varchar' );
    $this->AddCampo('justificativa' , 'varchar' );
    $this->AddCampo('timestamp_baixa','timestamp');

}

}
