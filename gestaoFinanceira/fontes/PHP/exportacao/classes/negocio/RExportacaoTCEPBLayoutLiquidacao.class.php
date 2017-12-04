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
    * Classe de Exportação Arquivos Principais
    * Data de Criação   : 11/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Exportador

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-01-04 10:24:50 -0200 (Qui, 04 Jan 2007) $

    * Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/01/04 12:24:50  bruce
desenvolvimento Exportação TCE_PB

Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

class RExportacaoTCEPBLayoutEmpenho
{
    public function RExportacaoTCEPBLayoutEmpenho()
    {
        $obExportador->addArquivo("EMPENHO.TXT");
        $obExportador->roUltimoArquivo->setTipoDocumento("ExportacaoTCE_PB");
        $obExportador->roUltimoArquivo->addBloco($arRecordSet['EMPENHO']);

    }

}
