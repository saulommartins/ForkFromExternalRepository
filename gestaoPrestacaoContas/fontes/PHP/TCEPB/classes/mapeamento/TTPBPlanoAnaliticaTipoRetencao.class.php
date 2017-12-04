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
    * Classe de mapeamento da tabela
    * Data de Criação: 24/01/2007

    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento
    
    $Id: TTPBPlanoAnaliticaTipoRetencao.class.php 59991 2014-09-24 18:37:59Z michel $

    * Casos de uso: uc-06.03.00

*/

/*
$Log$
Revision 1.2  2007/05/14 20:10:31  hboaventura
Arquivos para geração do TCEPB

Revision 1.1  2007/05/11 15:11:22  hboaventura
Arquivos para geração do TCEPB

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBPlanoAnaliticaTipoRetencao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TTPBPlanoAnaliticaTipoRetencao()
    {
        parent::Persistente();
        $this->setTabela("tcepb.plano_analitica_tipo_retencao");
        
        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio');
        
        $this->AddCampo( 'cod_plano' ,'integer' ,true, ''   ,true ,true  );
        $this->AddCampo( 'exercicio' ,'char' ,true, '4'   ,true ,true  );
        $this->AddCampo( 'cod_tipo' ,'integer' ,true, '' ,false ,false );
    }
}
