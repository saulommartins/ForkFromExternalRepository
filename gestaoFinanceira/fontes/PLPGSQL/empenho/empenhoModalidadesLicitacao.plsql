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
CREATE OR REPLACE FUNCTION empenhomodalidadeslicitacao(character varying, numeric, character varying, integer, character varying, integer, integer) RETURNS INTEGER AS $$
DECLARE                                                                                                                                                
    Exercicio       ALIAS FOR $1;    
    Valor           ALIAS FOR $2;        
    Complemento     ALIAS FOR $3;  
    CodLote         ALIAS FOR $4;      
    TipoLote        ALIAS FOR $5;     
    CodEntidade     ALIAS FOR $6;  
    CodPreEmpenho   ALIAS FOR $7;
                               
    Modalidade VARCHAR := '';   
    Sequencia INTEGER;          
BEGIN                         
    Modalidade := pegaEmpenhoEmpenhoModalidade(  Exercicio , CodPreEmpenho  );
    Modalidade := sem_acentos(Modalidade);

    IF EXERCICIO::integer = 2013 THEN
        IF   Modalidade  =  'Concurso' THEN                                                                                  
            Sequencia := FazerLancamento(  '622920401' , '522920401' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                           
        IF   Modalidade  =  'Convite' THEN                                                                                              
            Sequencia := FazerLancamento(  '622920402' , '522920402' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                            
        IF   Modalidade  =  'Tomada' THEN                                                                                                  
            Sequencia := FazerLancamento(  '622920403' , '522920403' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                           
        IF   Modalidade  =  'Concorrencia' THEN                                                                                             
            Sequencia := FazerLancamento(  '622920404' , '522920404' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                      
        IF   Modalidade  =  'Dispensa' THEN                                                                                             
            Sequencia := FazerLancamento(  '622920406' , '522920406' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                         
        IF   Modalidade  =  'Inexigivel' THEN                                                                                           
            Sequencia := FazerLancamento(  '622920407' , '522920407' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                             
        IF   Modalidade  =  'Nao Aplicavel' THEN                                                                                         
            Sequencia := FazerLancamento(  '622920408' , '522920408' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                       
        IF   Modalidade  =  'Suprimentos' THEN                                                                                       
            Sequencia := FazerLancamento(  '622920409' , '522920409' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                       
        IF   Modalidade  =  'Consulta' THEN                                                                                         
            Sequencia := FazerLancamento(  '622920411' , '522920411' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                        
        IF   Modalidade  =  'Pregao Presencial' THEN                                                                                  
            Sequencia := FazerLancamento(  '622920412' , '522920412' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                       
        IF   Modalidade  =  'Pregao Eletronico' THEN                                                                                        
            Sequencia := FazerLancamento(  '622920412' , '522920412' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                          
    END IF;
    
    IF EXERCICIO::INTEGER <= 2012 THEN
        IF   Modalidade  =  'Concurso' THEN                                                                                                                    
            Sequencia := FazerLancamento(  '192419900000000' , '192410201000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410201000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Convite' THEN                                                                                                                     
            Sequencia := FazerLancamento(  '192419900000000' , '192410202000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410202000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Tomada' THEN                                                                                                                      
            Sequencia := FazerLancamento(  '192419900000000' , '192410203000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410203000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Concorrencia' THEN                                                                                                                
            Sequencia := FazerLancamento(  '192419900000000' , '192410204000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410204000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Dispensa' THEN                                                                                                                    
            Sequencia := FazerLancamento(  '192419900000000' , '192410206000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410206000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Inexigivel' THEN                                                                                                                  
            Sequencia := FazerLancamento(  '192419900000000' , '192410207000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410207000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Nao Aplicavel' THEN                                                                                                               
            Sequencia := FazerLancamento(  '192419900000000' , '192410208000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410208000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Suprimentos' THEN                                                                                                                 
            Sequencia := FazerLancamento(  '192419900000000' , '192410209000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410209000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF    Modalidade  =  'Integracao' THEN                                                                                                                 
            Sequencia := FazerLancamento(  '192419900000000' , '192410210000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410210000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Pregao' THEN                                                                                                                      
            Sequencia := FazerLancamento(  '192419900000000' , '192410212000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410212000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Pregao Presencial' THEN                                                                                                           
            Sequencia := FazerLancamento(  '192419900000000' , '192410212000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410212000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Pregao Eletronico' THEN                                                                                                           
            Sequencia := FazerLancamento(  '192419900000000' , '192410212000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410212000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Chamada Publica' THEN                                                                                                             
            Sequencia := FazerLancamento(  '192419900000000' , '192410213000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410213000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
        IF   Modalidade  =  'Registro de Precos' THEN                                                                                                          
            Sequencia := FazerLancamento(  '192419900000000' , '192410214000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '292410214000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;                                                                                                                                                
    END IF;                                                                                                                                                
    RETURN Sequencia;                                                                                                                                      
END;                                                                     
$$ LANGUAGE 'plpgsql';
